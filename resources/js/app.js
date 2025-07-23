import {Livewire, Alpine} from '../../vendor/livewire/livewire/dist/livewire.esm'
import Datepicker from './components/datepicker-component'

Alpine.data('datepicker', Datepicker)

Livewire.start()
