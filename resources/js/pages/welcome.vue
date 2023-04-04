<template>
  <div>
    <div>
      <card title="Book your vehicle">
        <form @submit.prevent="sendBooking" @keydown="form.onKeydown($event)">
          <!-- Booking date -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">Booking date</label>
            <div class="col-md-7">
              <datepicker
                v-model="form.booking_date"
                :disabled-dates="state.disabledDates"
                :class="{ 'is-invalid': form.errors.has('booking_date') }"
                name="booking_date"
                :format="formatDate"
                @selected="getAvailableHours"
              />
              <has-error :form="form" field="booking_date" />
            </div>
          </div>

          <!-- Booking time -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">Booking time</label>
            <div class="col-md-7">
              <vue-timepicker
                v-model="form.booking_time" :minute-interval="30"
                :hour-range="hourRange"
                :class="{ 'is-invalid': form.errors.has('booking_time') }"
                name="booking_time"
                :disabled="disableTime"
                @error="timeErrorHandler"
              />
              <has-error :form="form" field="booking_time" />
            </div>
          </div>

          <!-- Name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">Name</label>
            <div class="col-md-7">
              <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" name="name">
              <has-error :form="form" field="name" />
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">Email</label>
            <div class="col-md-7">
              <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email">
              <has-error :form="form" field="email" />
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">Phone number</label>
            <div class="col-md-7">
              <input v-model="form.phone" :class="{ 'is-invalid': form.errors.has('phone') }" class="form-control" name="phone">
              <has-error :form="form" field="phone" />
            </div>
          </div>

          <!-- Vehicle make -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">Vehicle make</label>
            <div class="col-md-7">
              <input v-model="form.vehicle_make" :class="{ 'is-invalid': form.errors.has('vehicle_make') }" class="form-control" name="vehicle_make">
              <has-error :form="form" field="vehicle_make" />
            </div>
          </div>

          <!-- Vehicle model -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">Vehicle model</label>
            <div class="col-md-7">
              <input v-model="form.vehicle_model" :class="{ 'is-invalid': form.errors.has('vehicle_model') }" class="form-control" name="vehicle_model">
              <has-error :form="form" field="vehicle_model" />
            </div>
          </div>

          <div class="mb-3 row">
            <div class="col-md-7 offset-md-3 d-flex">
              <!-- Submit Button -->
              <v-button :loading="form.busy">
                Book now
              </v-button>
            </div>
          </div>
        </form>
      </card>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
import Datepicker from 'vuejs-datepicker'
import Form from 'vform'
import { helpers } from '~/helpers/general'

export default {

  components: {
    VueTimepicker,
    Datepicker
  },

  layout: 'default',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    form: new Form({
      booking_date: '',
      booking_time: '',
      vehicle_model: '',
      vehicle_make: '',
      phone: '',
      name: '',
      email: ''
    }),
    title: window.config.appName,
    hourRange: [[9, 17]],
    disableTime: true,
    state: {
      disabledDates: {
        to: new Date(), // Disable all dates up to specific date
        dates: [
          new Date(2023, 4, 5),
          new Date(2023, 9, 17)
        ],
        days: [6, 0]

      }
    }
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  }),

  created () {
    this.$store.dispatch('booking/fetchDisabledBookingDates').then((r) => this.state.disabledDates.dates = r)
  },

  methods: {
    async sendBooking () {
      this.form.booking_date = this.formatDate(this.form.booking_date)
      this.form.post('api/bookings')
        .then((data) => this.$toastr.s('Booking save', 'Success'))
        .catch((err) => this.$toastr.e('An an error was encountered, slot might have been taken', 'Error'))
    },
    async getAvailableHours (date) {
      await this.$store.dispatch('booking/fetchAvailableHours', date)
        .then((r) => {
          this.hourRange = r
          this.disableTime = false
        })
        .catch((e) => this.$toastr.e('Error encountered ' + e, 'Error'))
    },
    timeErrorHandler (eventData) {
      console.log('error', eventData)
    },

    formatDate (date) {
      return helpers.formatDate(date)
    }
  }
}
</script>

<style scoped>
.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

.title {
  font-size: 85px;
}
</style>
