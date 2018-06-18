import Vue from 'vue'
import VeeValidate, { Validator } from 'vee-validate'
import vi from 'vee-validate/dist/locale/vi';
const dictionary = {
	vi: {
		attributes: {
			name: 'Họ tên',
			password: 'Mật khẩu',
			confirmPassword: 'Xác nhận mật khẩu',
			birthday: 'Ngày sinh',
			phone: 'Số điện thoại',
			address: 'Địa chỉ',
			type: 'Thể loại',
			storeName: 'Tên cửa hàng',
			storePhone: 'Điện thoại cửa hàng',
			storeAddress: 'Địa chỉ cửa hàng',
			prepareTime: 'Thời gian chuẩn bị',
			city: 'Thành phố',
			district: 'Quận'

		}
	}
}
Validator.localize(dictionary);
Validator.localize('vi', vi);
Vue.use(VeeValidate)