(function($){
$.fn.pddTest = function($){

	var pdd = this;
	var test={};
	var s={};
    var o={};	
	
	jQuery.extend( o,{
		url: '/wp-admin/admin-ajax.php',
		current: '&#9675;',
		done: '&#10004;',
		error: '&#10006;',
		online: true,
		errorCount: 2,
		
		orderby: 'id',
		count: 20,
		action: 'get_question',
		theme: '',
		ticket: ''
	}, $)
	
	_init();

	function _init(){

		_prepare();
		_init_vars();
		_load_questions();
		_events();
		
	}

	function _prepare(){ 
		s.width = pdd.width();
		s.l_width = pdd.find('.left').width();
		s.r_width = pdd.width()-pdd.find('.left').width()-10;		
		pdd.find('.right').css({ width:s.r_width });
		pdd.find('.numbers').niceScroll();
	}

	function _load_questions(){
		show_loading();
		jQuery.post( 
			o.url, 
			{
				action: o.action,
				theme:  o.theme,
				ticket: o.ticket,
				count:  o.count,
				orderby: o.orderby
			}, 
			function(response){
				s.allQuestions.empty().append(response);
				_read_questions(response);
			}
		);	
	}
	
	function _init_vars(){
		s.testContainer 	 = pdd.find('.pdd-test-container');
		s.allQuestions  	 = pdd.find('.questions-container');
		s.questionContainer  = pdd.find('.question-content');
		s.numbersContainer   = pdd.find('.numbers');
		test.questions 		 = [];
		test.numbers  		 = [];
		test.result 	     = [];
		s.done				 = false;
		s.current			 = 0;
		s.count				 = 0;
	}
	
	
	// считывает вопросы в базу
	function _read_questions(){
		s.allQuestions.find('.question-item').each(function(i,e){
			var item = {};
			s.count++;
			jQuery(e).data('i',i);
			item.element  = jQuery(e);
			item.question = jQuery(e).find('.question').html();
			item.image 	  = jQuery(e).find('.image img').clone();			
			item.answers  = [];
			item.answers_li = [];
			jQuery(item.element).find('.answers li').each(function(ii,ee){
				if( jQuery(ee).data('status')=='yes' ){ item.answers[ii] = true; }
				else{ item.answers[ii] = false; }
				item.answers_li[ii] = jQuery(ee);
				jQuery(ee).removeAttr('data-status');
			})			
			item.answerGiven = -1; // ответ не был дан
			test.questions[i]  = item;
			test.numbers[i]    = {};
			test.numbers[i].li = jQuery('<li><a href="#" data-i="'+(i+1)+'">'+(i+1)+'</a></li>');
			s.numbersContainer.append( test.numbers[i].li );
			test.numbers[i].a  = s.numbersContainer.find('li:last a');
			test.numbers[i].a.click(function(){ changeQuestion(i); return false; }) // клик по номеру вопроса
		})
		showQuestion();
		hide_loading();
	}	
	
	function changeQuestion(index){
		index = get_current(index);
		if( index>-1 ){
			showQuestion(index);
			return true;
		}else{
			return false;
		}
	}
	
	function checkAnswer(index){
		if( test.questions[s.current].answerGiven==-1 ){
			if( test.questions[s.current].answers[index] ){
				test.result[s.current] = true;	
				test.numbers[s.current].a.addClass('correct'); 				
				test.questions[s.current].answers_li[index].addClass('correct');
			}else{
				test.result[s.current] = false;
				test.numbers[s.current].a.addClass('error'); 
				test.questions[s.current].answers_li[index].addClass('error');
				for( var i=0;i<test.questions[s.current].answers.length;i++){ // отмечаем корректный ответ
					if( test.questions[s.current].answers[i] ) { test.questions[s.current].answers_li[i].addClass('correct'); }
				}
			}
			jQuery(test.questions[s.current].element).find('.comment').removeClass('hide');
			test.questions[s.current].answerGiven = index;
			// если ответ верный, то автопереход
			if( test.result[s.current] ){
				// проверяем есть ли свободные вопросы
				if( !changeQuestion('next-free')  ){
					get_result();
				}
			}
		}
	}
	
	function get_result(){
		pdd.find('.right').addClass('hide');
		pdd.find('.left').addClass('hide');
		pdd.find('.result').removeClass('hide');
		var correctCount=0, errorCount=0, status_;
		for(var i=0;i<s.count;i++){
			if( test.result[i] ){  
				correctCount++;
			}else{
				errorCount++;
			}
		}		
		if( errorCount<=o.errorCount ){ // тест сдан
			pdd.find('.result').addClass('success');
			pdd.find('.result .title').text('Тест сдан');
			status_='yes';
		}else{ // тест не сдан
			pdd.find('.result').addClass('fail');
			pdd.find('.result .title').text('Тест не сдан');
			status_='no';
		}
		pdd.find('.result .descr .count b').text(s.count);
		pdd.find('.result .descr .correct b').text(correctCount);
		pdd.find('.result .descr .error b').text(errorCount);
		
		// отправить результаты на сервер
		var answers=[], questions=[];
		for(var i=0;i<s.count;i++){
			//questions.push();
			if( test.result[i] ){  }
		}
		jQuery.post( 
			o.url, 
			{
				theme:  o.theme,
				ticket: o.ticket,
				count:  o.count,
				action: 'add_to_profile',
				status: status_,
				questions: questions,
				answers: answers
			}, 
			function(response){
				
			}
		);	
	}
	
	function showQuestion(index){ 		
		index = get_current(index);
		if( index>-1 ){
			s.questionContainer.empty().append( test.questions[index].element );
			s.numbersContainer.find('.active').removeClass('active');
			jQuery(test.numbers[index].a).addClass('active');
			s.current = index;
		}else{
			return false;
		}
	}
	
	function get_current(index){
		var current=0;
		if( index=='next-free' ){
			for(var i=s.current+1;i<s.count;i++){
				if( test.questions[i].answerGiven==-1 ){ return i; }
			}
			for(var i=0;i<s.current;i++){
				if( test.questions[i].answerGiven==-1 ){ return i; }
			}
			return -1;
		}
		if( index=='next' ){ current=( s.current<s.count-1 )?s.current+1:0; }
		if( index=='prev' ){ current=( s.current>0 )?s.current-1:s.count-1; }
		if( jQuery.isNumeric(index) ){ current=( index>=0 && index<=s.count-1 )?index:0; }
		return current;
	}
		
	function _events(){
		pdd.on('click', '.next', function(){
			changeQuestion('next');
			return false;
		})
		
		pdd.on('click', '.prev', function(){
			changeQuestion('prev');
			return false;
		})
		
		pdd.on('click', '.answers a', function(){ 
			var index = parseInt( jQuery(this).data('answer') );
			checkAnswer(index); 
			return false; 
		}) 
		
		pdd.on('click', '.show-answers a', function(){ 
			jQuery(this).hide();
			for(var i=0;i<s.count;i++){
				pdd.find('.result .result-answers').append( test.questions[i].element );
			}
			return false; 
		}) 
		
		jQuery(window).keydown(function(event){        
			if (event.ctrlKey) {
				var link = null;
					switch (event.keyCode ? event.keyCode : event.which ? event.which : null) {                      
						case 0x25:
							changeQuestion('prev');
							break;
						case 0x27:
							changeQuestion('next');
							break;           
						}
					if (link && link.attr('href')) document.location = link.attr('href');
			}  
		});
		
		pdd.on('click', '.answers a', function(){
			//var i=parseInt(jQuery(this).data('i'));
			//s.numbersContainer.find('.active').removeClass('active');
			//s.numbersContainer.find('a[data-i="'+i+'"]').addClass('active');
			//jQuery('.pdd-test-container .question-content').empty().append( test.questions[i].element );
			return false;
		})
		
		setInterval(function(){
			if( s.width!=pdd.width() ){ _prepare(); }

		},0)
	}
	
	function show_loading(){
		pdd.find('.buttons-cover').addClass('hide');
		pdd.find('.question-content').addClass('loading');
	}
	
	function hide_loading(){
		pdd.find('.buttons-cover').removeClass('hide');
		pdd.find('.question-content').removeClass('loading');
	}

	/*$.post( 
		myajax.url, 
		arg.data, 
		function(response) {
			arg.callback(response);
		}
	);*/

}
})(jQuery);