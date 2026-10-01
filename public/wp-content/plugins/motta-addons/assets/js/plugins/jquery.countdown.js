/**
 * jQuery Countdown plugin 1.0.2
 *
 * A simple countdown timer plugin that displays weeks, days, hours, minutes, and seconds
 *
 * @author Drfuri
 */
(function ($) {
	const SECOND = 1;
	const MINUTE = 60 * SECOND;
	const HOUR = 60 * MINUTE;
	const DAY = 24 * HOUR;
	const WEEK = 7 * DAY;

	const DEFAULT_OPTIONS = {
		format: 'full', // 'full', 'short', 'minimal'
		onTick: null, // Callback function executed every second
		onComplete: null, // Callback function executed when countdown ends
		autoStart: true // Whether to start countdown automatically
	};

	/**
	 * Calculate time units from total seconds
	 * @param {number} distance - Total seconds remaining
	 * @returns {Object} Time units object
	 */
	function calculateTimeUnits(distance) {
		return {
			weeks: Math.floor(distance / WEEK),
			days: Math.floor((distance % WEEK) / DAY),
			hours: Math.floor((distance % DAY) / HOUR),
			minutes: Math.floor((distance % HOUR) / MINUTE),
			seconds: Math.floor(distance % MINUTE)
		};
	}

	/**
	 * Calculate next target time for repeating countdowns
	 * @param {string} repeatType - 'daily' or 'weekly'
	 * @returns {number} Seconds until next target time
	 */
	function calculateRepeatingTarget(repeatType) {
		const now = new Date();
		let target;

		if (repeatType === 'daily') {
			// Set target to next day at 00:00
			target = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
		} else if (repeatType === 'weekly') {
			// Set target to next Monday at 00:00
			const daysUntilMonday = (8 - now.getDay()) % 7;
			target = new Date(now.getFullYear(), now.getMonth(), now.getDate() + daysUntilMonday);
		}

		// Set time to midnight (00:00:00)
		target.setHours(0, 0, 0, 0);

		return Math.floor((target - now) / 1000);
	}

	/**
	 * Format number with leading zero if needed
	 * @param {number} num - Number to format
	 * @returns {string} Formatted number
	 */
	function padNumber(num) {
		return num < 10 ? '0' + num : num.toString();
	}

	/**
	 * Generate HTML for a single time unit
	 * @param {string} unit - Unit name (weeks, days, etc)
	 * @param {number} value - Unit value
	 * @param {string} text - Unit label
	 * @param {boolean} showDivider - Whether to show divider
	 * @param {boolean} hidden - Whether to hide the unit
	 * @returns {string} HTML string
	 */
	function generateTimeUnit(unit, value, text, showDivider, hidden) {
		showDivider = showDivider || false;
		hidden = hidden || false;

		return [
			'<span class="' + unit + ' timer ' + (hidden ? 'hidden' : '') + '">',
			'    <span class="digits">' + value + '</span>',
			'    <span class="text">' + text + '</span>',
			showDivider ? '    <span class="divider">:</span>' : '',
			'</span>'
		].join('\n');
	}

	/**
	 * Generate HTML for timer display
	 * @param {Object} units - Time units object
	 * @param {Object} texts - Translation texts
	 * @param {string} format - Display format
	 * @returns {string} HTML string
	 */
	function generateTimerHTML(units, texts, format) {
		var parts = [];
		var weeks = units.weeks;
		var days = units.days;
		var hours = units.hours;
		var minutes = units.minutes;
		var seconds = units.seconds;

		// For minimal format, only show the most significant non-zero unit
		if (format === 'minimal') {
			if (weeks > 0) return generateTimeUnit('weeks', weeks, texts.weeks);
			if (days > 0) return generateTimeUnit('days', days, texts.days);
			if (hours > 0) return generateTimeUnit('hours', hours, texts.hours);
			if (minutes > 0) return generateTimeUnit('minutes', minutes, texts.minutes);
			return generateTimeUnit('seconds', seconds, texts.seconds);
		}

		// For short format, hide zero values except hours/minutes/seconds
		if (format === 'short') {
			if (weeks > 0) parts.push(generateTimeUnit('weeks', weeks, texts.weeks, true));
			if (days > 0) parts.push(generateTimeUnit('days', days, texts.days, true));
			parts.push(generateTimeUnit('hours', padNumber(hours), texts.hours, true));
			parts.push(generateTimeUnit('minutes', padNumber(minutes), texts.minutes, true));
			parts.push(generateTimeUnit('seconds', padNumber(seconds), texts.seconds));
			return parts.join('');
		}

		// Full format - show all units
		parts = [
			generateTimeUnit('weeks', weeks, texts.weeks, true, weeks === 0),
			generateTimeUnit('days', days, texts.days, true, days === 0),
			generateTimeUnit('hours', padNumber(hours), texts.hours, true),
			generateTimeUnit('minutes', padNumber(minutes), texts.minutes, true),
			generateTimeUnit('seconds', padNumber(seconds), texts.seconds)
		];
		return parts.join('');
	}

	$.fn.motta_countdown = function (customOptions) {
		customOptions = customOptions || {};
		var options = $.extend({}, DEFAULT_OPTIONS, customOptions);

		return this.each(function () {
			var $element = $(this);
			var remainingTime = $element.data('expire');
			var texts = $element.data('text');
			var repeatType = $element.data('repeat');
			var isPaused = false;
			var countdown;

			// For repeating countdowns, calculate initial remaining time
			if (repeatType && (repeatType === 'daily' || repeatType === 'weekly')) {
				remainingTime = calculateRepeatingTarget(repeatType);
			}

			// Validate input data
			if (typeof remainingTime !== 'number' || remainingTime < 0) {
				console.error('Invalid countdown time provided');
				return;
			}

			if (!texts || typeof texts !== 'object') {
				console.error('Invalid or missing translation texts');
				return;
			}

			function updateClock(distance) {
				if (distance < 0) {
					// For repeating countdowns, recalculate next target time
					if (repeatType) {
						remainingTime = calculateRepeatingTarget(repeatType);
						return true;
					}

					if (options.onComplete) options.onComplete.call($element);
					return false;
				}

				var units = calculateTimeUnits(distance);
				$element.html(generateTimerHTML(units, texts, options.format));

				if (options.onTick) options.onTick.call($element, units);
				return true;
			}

			function startCountdown() {
				isPaused = false;
				updateClock(remainingTime);
				countdown = setInterval(function() {
					if (!isPaused) {
						remainingTime--;
						if (!updateClock(remainingTime)) {
							clearInterval(countdown);
						}
					}
				}, 1000);
			}

			// Public methods
			$element.countdown = {
				pause: function() {
					isPaused = true;
				},
				resume: function() {
					isPaused = false;
				},
				restart: function(newTime) {
					clearInterval(countdown);
					if (repeatType) {
						remainingTime = calculateRepeatingTarget(repeatType);
					} else {
						remainingTime = typeof newTime === 'number' ? newTime : $element.data('expire');
					}
					startCountdown();
				},
				destroy: function() {
					clearInterval(countdown);
					$element.html('');
					delete $element.countdown;
				}
			};

			// Start countdown if autoStart is true
			if (options.autoStart) {
				startCountdown();
			}
		});
	};

	// Initialize countdown timers
	$(function () {
		$('.motta-countdown').motta_countdown();

		// Handle dynamic countdown initialization
		$(document.body).on('motta_countdown', function(e, $el) {
			$el.motta_countdown();
		});
	});
})(jQuery);
