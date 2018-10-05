import {imageUrl} from '@/config.js'
export default {
	methods: {
		image(url) {
			if(url == null) {
				return imageUrl+'/img/default.png'
			} else {
				if(url.slice(1, 8) === "storage") {
					return imageUrl+url
				} else {
					return url
				}
			}
		},	
	},
	computed: {
		cameraOverlay: function() {
			return this.$vuetify.breakpoint.smAndDown ? `height: 30%` : `height: 100%`
		}
	}
}