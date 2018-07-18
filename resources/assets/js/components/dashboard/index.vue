<template>
	<div>
		<!-- <v-btn color="success" @click="send">Send mail</v-btn> -->
	</div>
</template>

<script>
import axios from 'axios'
export default {
	methods: {
		send: function() {
			axios.get('/api/SendMail').then(response => {
				console.log(response)
			})
		}
	}
}
</script>