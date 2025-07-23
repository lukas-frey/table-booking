import AirDatepicker from 'air-datepicker'
import en from 'air-datepicker/locale/en'

export default ({
                    state,
                }) => ({
    init() {
        new AirDatepicker(this.$el, {
            locale: en,
            firstDay: 1
        })
    }
})
