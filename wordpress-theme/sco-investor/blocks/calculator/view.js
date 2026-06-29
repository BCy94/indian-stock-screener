/* sci/calculator — front-end behavior. Ported from the static prototype's
 * main.js; generalized from a single document.querySelector to
 * querySelectorAll since a block can in principle appear more than once
 * per page, unlike the static page it was lifted from. */
( function () {
	'use strict';

	function fmtINR( n ) {
		return '₹' + Math.round( n ).toLocaleString( 'en-IN' );
	}

	function initCalculator( calc ) {
		// Reads the actual initial active tab rather than assuming "sip",
		// since the block's defaultMode attribute can make Lumpsum the
		// one marked .active in the rendered markup.
		var calcMode = 'sip';
		var activeTab = calc.querySelector( '[data-calc-mode].active' );
		if ( activeTab ) calcMode = activeTab.getAttribute( 'data-calc-mode' );

		function calcGet( name ) {
			var el = calc.querySelector( '[data-calc-input="' + name + '"]' );
			return el ? parseFloat( el.value ) || 0 : 0;
		}

		function runCalc() {
			var rate = calcGet( 'rate' );
			var years = calcGet( 'years' );
			var monthlyRate = rate / 100 / 12;
			var invested, total;
			var series = [];

			if ( calcMode === 'sip' ) {
				var amount = calcGet( 'amount' );
				invested = amount * years * 12;
				for ( var y = 1; y <= years; y++ ) {
					var n = y * 12;
					var fv = monthlyRate > 0
						? amount * ( ( Math.pow( 1 + monthlyRate, n ) - 1 ) / monthlyRate ) * ( 1 + monthlyRate )
						: amount * n;
					series.push( fv );
				}
			} else {
				var lumpsum = calcGet( 'lumpsum' );
				invested = lumpsum;
				for ( var y2 = 1; y2 <= years; y2++ ) {
					series.push( lumpsum * Math.pow( 1 + rate / 100, y2 ) );
				}
			}
			total = series.length ? series[ series.length - 1 ] : 0;
			var returns = total - invested;

			var totalOut = calc.querySelector( '[data-calc-out="total"]' );
			var investedOut = calc.querySelector( '[data-calc-out="invested"]' );
			var returnsOut = calc.querySelector( '[data-calc-out="returns"]' );
			var barOut = calc.querySelector( '[data-calc-out="bar"]' );
			if ( totalOut ) totalOut.textContent = fmtINR( total );
			if ( investedOut ) investedOut.textContent = fmtINR( invested );
			if ( returnsOut ) returnsOut.textContent = fmtINR( returns );
			if ( barOut ) barOut.style.width = ( total > 0 ? Math.min( 100, ( invested / total ) * 100 ) : 0 ) + '%';

			var chart = calc.querySelector( '[data-calc-out="chart"]' );
			if ( chart && series.length ) {
				var max = Math.max.apply( null, series );
				var w = 300, h = 140;
				var pts = series.map( function ( v, i ) {
					var x = series.length === 1 ? w : ( i / ( series.length - 1 ) ) * w;
					var py = h - ( max > 0 ? ( v / max ) * ( h - 10 ) : 0 ) - 4;
					return [ x, py ];
				} );
				var line = 'M' + pts.map( function ( p ) { return p[ 0 ].toFixed( 1 ) + ',' + p[ 1 ].toFixed( 1 ); } ).join( ' L' );
				var linePath = chart.querySelector( 'path.line' );
				var fillPath = chart.querySelector( 'path.fill' );
				if ( linePath ) linePath.setAttribute( 'd', line );
				if ( fillPath ) fillPath.setAttribute( 'd', line + ' L' + w + ',' + h + ' L0,' + h + ' Z' );
			}
		}

		calc.querySelectorAll( '[data-calc-input]' ).forEach( function ( input ) {
			input.addEventListener( 'input', function () {
				var name = input.getAttribute( 'data-calc-input' );
				var slider = calc.querySelector( '[data-calc-slider="' + name + '"]' );
				if ( slider ) slider.value = input.value;
				runCalc();
			} );
		} );
		calc.querySelectorAll( '[data-calc-slider]' ).forEach( function ( slider ) {
			slider.addEventListener( 'input', function () {
				var name = slider.getAttribute( 'data-calc-slider' );
				var input = calc.querySelector( '[data-calc-input="' + name + '"]' );
				if ( input ) input.value = slider.value;
				runCalc();
			} );
		} );

		var calcTabs = calc.querySelectorAll( '[data-calc-mode]' );
		calcTabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				calcTabs.forEach( function ( t ) { t.classList.remove( 'active' ); } );
				tab.classList.add( 'active' );
				calcMode = tab.getAttribute( 'data-calc-mode' );
				calc.querySelectorAll( '[data-calc-mode-field]' ).forEach( function ( field ) {
					field.hidden = field.getAttribute( 'data-calc-mode-field' ) !== calcMode;
				} );
				runCalc();
			} );
		} );

		runCalc();
	}

	document.querySelectorAll( '[data-calculator]' ).forEach( initCalculator );
} )();
