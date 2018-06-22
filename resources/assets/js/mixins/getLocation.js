export default {
	methods: {
		getLocation: (value) => new Promise((resolve, reject) => {
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode({'address': value}, function(response, status) {
				if(status === 'OK') {
					resolve(response)
				} else {
					reject('Geocode was not successful for the following reason: ' + status)
				}
			})
		}),
		typeIcon: function(value) {
                       var status = new String(value).toLowerCase()
                       switch(status) {
                              case 'quán ăn':
                              return {url: '/img/qa_blue.png'}
                              break
                              case 'trà sữa':
                              return {url: '/img/ts_blue.png'}
                              break
                              case 'cà phê':
                              return {url: '/img/cp_blue.png'}
                              break
                              case 'ăn vặt':
                              return {url: '/img/av_blue.png'}
                              break
                              case 'thức ăn nhanh':
                              return {url: '/img/ff_blue.png'}
                              break
                              case 'vỉa hè':
                              return {url: '/img/vh_blue.png'}
                              break
                      }
        },
	}
}