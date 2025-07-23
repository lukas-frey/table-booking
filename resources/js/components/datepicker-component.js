import AirDatepicker from 'air-datepicker'
import en from 'air-datepicker/locale/en'

export default ({
                    state,
                    minDate,
                    maxDate,
                    disabledDates,
                }) => ({
    state: state,

    init() {
        new AirDatepicker(this.$el, {
            locale: en,
            firstDay: 1,
            fixedHeight: true,
            minDate: minDate,
            maxDate: maxDate,

            onSelect: ({date}) => {
                this.state = date
            },

            onRenderCell: ({date, cellType}) => {
                if (cellType === 'day') {
                    // if (disabledDates.includes(date.toISOString().slice(0, 10))) {
                    if (this.isDateDisabled(date)) {
                        return {
                            disabled: true,
                            classes: 'disabled-class',
                            attrs: {
                                title: 'Cell is disabled'
                            }
                        }
                    }
                }
            }
        })
    },

    isDateDisabled(date) {
        // Convert the date object to YYYY-MM-DD format
        var year = date.getFullYear();
        var month = (date.getMonth() + 1).toString().padStart(2, '0'); // months are 0-indexed
        var day = date.getDate().toString().padStart(2, '0');

        var formattedDate = `${year}-${month}-${day}`;

        // Check if the formatted date is in the array
        return disabledDates.includes(formattedDate);
    }
})
