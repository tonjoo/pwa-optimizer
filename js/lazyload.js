(function($){
	let elements = document.querySelectorAll('source, img, iframe');

	if ('IntersectionObserver' in window) {
		// IntersectionObserver Supported

		// console.log( 'Run IntersectionObserver' );

		let config = {
			root: null,
			rootMargin: TONJOO_PWA.intersection_observer.root_margin+'px',
			threshold: TONJOO_PWA.intersection_observer.threshold
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
		element.classList.add('fade-in');
		element.classList.remove('lazy-hidden');
		if(element.dataset && element.dataset.src) {
			element.src = element.dataset.src;
		}

		if(element.dataset && element.dataset.srcset) {
			element.srcset = element.dataset.srcset;
		}
	}
}(jQuery));
