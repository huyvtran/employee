<template>
	<v-content>
		<v-card flat>
			<v-subheader>
				Thời gian hoạt động
				<v-tooltip top>
					<v-btn slot="activator" flat icon color="green" @click.stop.prevent="$store.commit('DIALOG_ACTIVITY')">
						<v-icon>edit</v-icon>
					</v-btn>
					<span>Cập nhật thời gian hoạt động</span>
				</v-tooltip>						
			</v-subheader>
			<v-layout row wrap>
				<v-flex  v-for="(item, index) in store.activities" :key="index">
					<v-card>
						<div class="text-xs-center grey lighten-3">									
							{{item.daysofweek}}
						</div>
						<v-divider></v-divider>
						<v-flex v-for="(time, i) in item.times" :key="i" class="text-xs-center">
							<div><strong>{{time.from}} - {{time.to}}</strong></div>
						</v-flex>				
					</v-card>							
				</v-flex>							
			</v-layout>					
		</v-card>
		<v-card class="mt-2">
			<GmapMap :center="{lat:store.lat, lng:store.lng}" :zoom="17" map-type-id="terrain" style="width: 100%; height: 500px">
				<GmapMarker	:position="{lat:store.lat, lng:store.lng}"
				:clickable="true" :icon="typeIcon(store.type_name)">
				<gmap-info-window :position="{lat:store.lat, lng:store.lng}" :opened="true">
					<v-card>
						<v-card-title primary-title>
							<div>
								<a class="headline font-weight-bold" :href="`https://www.dofuu.com/${store.city_slug}/${store.slug}`" target="_blank">{{store.name}}</a>
								<div class="grey--text">{{store.type_name}}</div>
							</div>
						</v-card-title>
						<v-list dense>
							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="indigo">phone</v-icon>
								</v-list-tile-action>
								<v-list-tile-content>
									<v-list-tile-title><h4>{{store.phone | formatPhone}}</h4></v-list-tile-title>
									<v-list-tile-sub-title><h4>Mobile</h4></v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="indigo">place</v-icon>
								</v-list-tile-action>
								<v-list-tile-content>
									<v-list-tile-title><h4>{{store.address}}</h4></v-list-tile-title>
									<v-list-tile-sub-title><h4>Address</h4></v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>
					</v-card>	
				</gmap-info-window>
			</GmapMarker>
		</GmapMap>
	</v-card>
	<vue-activity-dialog :store.sync="store"></vue-activity-dialog>
	<v-snackbar :timeout="3000" :color="alert.type" bottom multi-line :value="alert.show">
		{{alert.message}}
		<v-btn flat @click.native="snackbar = false">Close</v-btn>
	</v-snackbar>
</v-content>
</template>

<script>
import {mapState} from 'vuex'
import index from '@/mixins/index'
import getLocation from '@/mixins/getLocation'
import ActivityDialog from '@/components/template/activityDialog'
import axios from 'axios'
// import Dialog from './dialog'
export default {
	mixins: [index, getLocation],
	data() {
		return {

		}
	},
	components: {
		'vue-activity-dialog': ActivityDialog
	},
	methods: {


	},
	computed: {
		...mapState({
			store: state => state.storeStore.store,
			alert: state => state.activityStore.alert
		})
	},
	created() {

	}
}	
</script>

