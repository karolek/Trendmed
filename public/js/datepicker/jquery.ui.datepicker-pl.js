/* Polish initialisation for the jQuery UI date picker plugin. */
/* Written by Jacek Wysocki (jacek.wysocki@gmail.com). */
jQuery(function($){
	$.datepicker.regional['pl'] = {
		closeText: 'Zamknij',
		prevText: '&#x3c;Poprzedni',
		nextText: 'NastÄpny&#x3e;',
		currentText: 'DziĹ',
		monthNames: ['StyczeĹ','Luty','Marzec','KwiecieĹ','Maj','Czerwiec',
		'Lipiec','SierpieĹ','WrzesieĹ','PaĹşdziernik','Listopad','GrudzieĹ'],
		monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
		'Lip','Sie','Wrz','Pa','Lis','Gru'],
		dayNames: ['Niedziela','Poniedzialek','Wtorek','Ĺroda','Czwartek','PiÄtek','Sobota'],
		dayNamesShort: ['Nie','Pn','Wt','Ĺr','Czw','Pt','So'],
		dayNamesMin: ['N','Pn','Wt','Ĺr','Cz','Pt','So'],
		weekHeader: 'Tydz',
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['pl']);
});