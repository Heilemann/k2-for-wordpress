<?php require('header.php'); ?>

/*	Thank you Drew McLellan for starting us off
	with http://24ways.org/2006/tasty-text-trimmer	*/

var k2Trimmer = {
	minValue: 0,
	maxValue: 100,
	chunks: false,
	prevValue: 0,

	setup: function(value) {
		k2Trimmer.chunks = false;

		if (value >= k2Trimmer.maxValue) {
			k2Trimmer.curValue = k2Trimmer.maxValue;
		} else if (value < k2Trimmer.minValue) {
			k2Trimmer.curValue = k2Trimmer.minValue;
		} else {
			k2Trimmer.curValue = value;
		}

		k2Trimmer.slider = new K2Slider('#trimmerhandle', '#trimmertrack', {
			minimum: 0,
			maximum: 10,
			value: 10,
			onSlide: function(x) {
				k2Trimmer.doTrim(x * 10);
			},
			onChange: function(x) {
				k2Trimmer.doTrim(x * 10);
			}
		});

		jQuery('#trimmermore').click(function() {
			k2Trimmer.slider.setValueBy(1);

			return false;
		});


		jQuery('#trimmerless').click(function() {
			k2Trimmer.slider.setValueBy(-1);

			return false;
		});

		jQuery('#trimmertrim').click(function() {
			k2Trimmer.slider.setValue(0);

			return false;
		});

		jQuery('#trimmeruntrim').click(function() {
			k2Trimmer.slider.setValue(100);

			return false;
		});
	},

	trimAgain: function() {
		k2Trimmer.loadChunks();
		k2Trimmer.doTrim(k2Trimmer.curValue);
	},

    loadChunks: function() {
		var everything = jQuery('#dynamic-content .entry-content');

		k2Trimmer.chunks = [];

		for (i=0; i<everything.length; i++) {
			k2Trimmer.chunks.push({
				ref: everything[i],
				html: jQuery(everything[i]).html(),
				text: jQuery.trim(jQuery(everything[i]).text())
			});
		}
	},

    doTrim: function(interval) {
		/* Spit out the trimmed text */
		if (!k2Trimmer.chunks)
			k2Trimmer.loadChunks();

		/* var interval = parseInt(interval); */
		k2Trimmer.curValue = interval;

		for (i=0; i<k2Trimmer.chunks.length; i++) {
			if (interval == k2Trimmer.maxValue) {
				jQuery(k2Trimmer.chunks[i].ref).html(k2Trimmer.chunks[i].html);
			} else if (interval == k2Trimmer.minValue) {
				jQuery(k2Trimmer.chunks[i].ref).html('');
			} else {
				var a = k2Trimmer.chunks[i].text.split(' ');
				a = a.slice(0, Math.round(interval * a.length / 100));
				jQuery(k2Trimmer.chunks[i].ref).html('<p>' + a.join(' ') + '&nbsp;[...]</p>');
			}
		}

		/* Add 'trimmed' class to <BODY> while active */
		if (k2Trimmer.curValue != k2Trimmer.maxValue) {
			jQuery('#dynamic-content').addClass("trimmed");
		} else {
			jQuery('#dynamic-content').removeClass("trimmed");
		}
	}
};