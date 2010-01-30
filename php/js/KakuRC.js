/*
 * kakurc, room control software
 * Copyright (C) 2010
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var KakuRC = new function () {
	var menuHandlers          = null;
	var webcams               = null;
	var webcamsTimer          = null;
	var webcamsInterval       = 5000;
	
	this.makeDynamicUrl = function (url) {
		// make a url like http://lala/ to http://lala/?7398723
		// or an url like http://lala/?id=50 to http://lala?id=50&7398723
		return url + (url.indexOf('?') > -1 ? '&' : '?') + (new Date().getTime() + Math.random());
	}
	
	this.updateWebcam = function(id) {
		// if the webcam is not already loading, load it
		if (!KakuRC.webcams[id].loading) {
			KakuRC.webcams[id].loading = true;
			
			// build a url
			var url = KakuRC.makeDynamicUrl(KakuRC.webcams[id].url);
			
			// create a new cam image
			var $newCam = $(new Image());
			
			// find the old style
			var style = $('#' + id + ' img').attr('style');
			
			// apply the old style to the new cam, plus some more options
			$newCam.attr('style', style);
			$newCam.css('opacity', 1);
			
			// add a load event
			$newCam.load(function () {
				KakuRC.webcams[id].loading = false;
				clearTimeout(KakuRC.webcams[id].errorTimer);
				KakuRC.webcams[id].errorTimer = null;
				
				// clean up old images
				$('#' + id + ' img').remove();
				
				// show the new image
				$('#' + id).show();
				$('#' + id).append($newCam);
				
				// fade out error and shadow divs
				$('#error-'  + id).fadeOut('medium');
				$('#shadow-' + id).fadeOut('medium');
				
				// clean up
				$newCam.unbind();
				$newCam = undefined;
			});
			
			$newCam.error(function () {
				KakuRC.webcams[id].loading = false;
				clearTimeout(KakuRC.webcams[id].errorTimer);
				KakuRC.webcams[id].errorTimer = null;
				
				// show error and shadow divs
				$('#error-'  + id).fadeIn('medium');
				$('#shadow-' + id).fadeIn('medium');
				
				// clean up
				if ($newCam != undefined) {
					$newCam.unbind();
					$newCam = undefined;
				}
			});
			
			KakuRC.webcams[id].errorTimer = function() {
				if (KakuRC.webcams[id].loading) {
					if ($newCam != undefined) {
						$newCam.error();
					}
				}
			};
			setTimeout(KakuRC.webcams[id].errorTimer, 5000);
			
			// load the new cam image
			$newCam.attr('src', url);
		}
	}
	
	this.updateWebcams = function() {
		// clear old timers
		if (KakuRC.webcamsTimer!=null) {
			clearTimeout(KakuRC.webcamsTimer);
			KakuRC.webcamsTimer = null;
		}
		
		$('#webcam-timer').clearQueue();
		$('#webcam-timer').css('width', 0);
		
		if (KakuRC.webcamsInterval < 10)
		{
			$('#webcam-timer').animate({width: (25*KakuRC.webcamsInterval)}, KakuRC.webcamsInterval*1000, 'linear');
			
			KakuRC.webcamsTimer = setTimeout(function() { KakuRC.updateWebcams() }, KakuRC.webcamsInterval*1000);
			
			for (var id in KakuRC.webcams) {
				KakuRC.updateWebcam(id);
			}
		}
	}

	this.init = function () {
		KakuRC.menuHandlers = new Array();
		KakuRC.webcams      = new Array();
	}
	
	this.getIntervalText = function(interval) {
		return (interval < 10 ? (interval >= 1 ? interval + ' sec' : 'live') : 'static');
	}

	this.start = function () {
		KakuRC.updateWebcams();
		
		$('#webcam-slider').slider({
			value: KakuRC.webcamsInterval,
			min: 0,
			max: 10,
			step: 1,
			animate: 'fast',
			slide: function(event, ui) {
				$('#webcam-interval').text(KakuRC.getIntervalText(ui.value));
				KakuRC.webcamsInterval = (ui.value >= 1 ? ui.value : 0.3);
				KakuRC.updateWebcams();
			}
		});
		$('#webcam-interval').text(KakuRC.getIntervalText(KakuRC.webcamsInterval));
	}
	
	this.addMenuHandler = function(menuId, linkId, optionId, typeId) {
		$('#' + linkId).click(function(){
			$('#' + menuId + ' .loader').fadeIn('fast');
			$.ajax({
				url: 'execute.php',
				data: {option: optionId, type: typeId},
				complete: function(){
					$('#' + menuId + ' .loader').fadeOut('medium');
				}
			});
		})
	}
	
	this.addWebcam = function (id, url) {
		KakuRC.webcams[id] = {url: url, loading: false, errorTimer: null};
	}
	
	this.setWebcamsInterval = function (value) {
		KakuRC.webcamsInterval = value/1000;
	}
	
};