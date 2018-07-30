(function($){
	let elements = document.querySelectorAll('source, img, iframe');

	if ('IntersectionObserver' in window) {
		// IntersectionObserver Supported

		// console.log( 'Run IntersectionObserver' );

		rootMargin = '0px';
		threshold = 0;
		if( "undefined" !== typeof(TONJOO_PWA) ){
			if( "undefined" !== typeof(TONJOO_PWA.intersection_observer) ){
				rootMargin = TONJOO_PWA.intersection_observer.root_margin+'px';
				threshold = TONJOO_PWA.intersection_observer.threshold;
			}
		} else {
			console.log( 'TONJOO_PWA is not defined.' );
		}

		let config = {
			root: null,
			rootMargin: rootMargin,
			threshold: threshold
		};

		let observer = new IntersectionObserver(onChange, config);
		elements.forEach(element => observer.observe(element));

		function onChange(changes, observer) {
			changes.forEach(change => {
				if (change.intersectionRatio > 0) {
					// Stop watching and load the source
					loadSource(change.target);
					observer.unobserve(change.target);
				}
			});
		}
	} else {
		// IntersectionObserver NOT Supported
		elements.forEach(element => loadSource(element));

		console.log( 'IntersectionObserver is NOT Supported' );
	}

	function loadSource(element) {
		if(element.dataset) {
			if(element.dataset.src) {
				element.src = element.dataset.src;
			}

			if(element.dataset.srcset) {
				element.srcset = element.dataset.srcset;
			}

			element.onload = function() {
				element.classList.add('fade-in');
				element.classList.remove('lazy-hidden');
				element.parentElement.classList.add('fade-in');
			}
		}
	}
}(jQuery));
