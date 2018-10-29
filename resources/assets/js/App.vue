<template>
	<div>
		<router-view></router-view>
		<vue-broadcasting v-if="isAuthentication" ref="broadcasting"/>
	</div>
</template>

<script>
import axios from 'axios'
import Broadcast from '@/components/broadcasting/Broadcast'
import {mapState} from 'vuex'

export default{
	components: {
		'vue-broadcasting' : Broadcast
	},
	computed: {
		...mapState({
			isAuthentication: state => state.authStore.isAuth,
			progress: state         => state.progress,
			currentUser: state      => state.authStore.authUser
		})
	},
	methods: {
		getNotification: function() {
			const data = {confirm: true}
			axios.post('/api/GetNotification', data).then(response => {
				if(response.status == 200) {
					this.$store.commit('FETCH_NOTIFICATION', response.data)
				}
			})
		},
	},
	mounted: function() {
		if(this.isAuthentication) {			
			this.getNotification()
		}
	}
}

</script>

<style>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  opacity: .5;
  position: absolute;
  width: 100%;
}
.border--radius {
	border-radius: 8px
}
</style>