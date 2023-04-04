<template>
  <div>
    <div class="row mb-3">
      <div class="col-lg-10 m-auto">
        <card title="Bookings">
          <template v-if="bookings && bookings.length > 0">
            <table>
              <thead>
                <th />
                <th>Vehicle make</th>
                <th>Vehicle model</th>
                <th>Date</th>
              </thead>

              <tbody>
                <tr v-for="booking, key in bookings">
                  <td>{{ (key + 1) }}</td>
                  <td>{{ booking.vehicle_make }}</td>
                  <td>{{ booking.vehicle_model }}</td>
                  <td>{{ booking.start_time }} - {{ booking.end_time }}</td>
                </tr>
              </tbody>
            </table>
          </template>
          <template v-else>
            <p>No bookings found </p>
          </template>
        </card>
      </div>
    </div>

    <div v-if="user.is_admin" class="row mb-3">
      <div class="col-lg-10 m-auto">
        <card title="Admin">
          <form @submit.prevent="manageBookings" @keydown="form.onKeydown($event)">
            <div class="mb-3 row">
              <label class="col-md-3 col-form-label text-md-end">Reserve day or time</label>
              <div class="col-md-7">
                <select v-model="form.reserve_type" :class="{ 'is-invalid': form.errors.has('reserve_type') }" class="form-control" name="reserve_type">
                  <option value="" disabled>Select...</option>
                  <option value="day">Day</option>
                  <option value="time">Time</option>
                </select>
                <has-error :form="form" field="reservedDateOrTime" />
              </div>
            </div>
            <div v-if="showFields">
              <!-- Booking date -->
              <div class="mb-3 row">
                <label class="col-md-3 col-form-label text-md-end">Booking date</label>
                <div class="col-md-7">
                  <datepicker
                    v-model="form.booking_date"
                    :disabled-dates="state.disabledDates"
                    :class="{ 'is-invalid': form.errors.has('booking_date') }"
                    name="booking_date"
                    @selected="getAvailableHours"
                  />
                  <has-error :form="form" field="booking_date" />
                </div>
              </div>

              <div class="mb-3 row" v-if="showTime">
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

              <div class="mb-3 row">
                <div class="col-md-7 offset-md-3 d-flex">
                  <!-- Submit Button -->
                  <v-button :loading="form.busy">
                    Reserve
                  </v-button>
                </div>
              </div>
            </div>
          </form>
        </card>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import Datepicker from 'vuejs-datepicker'
import Form from 'vform'
import VueTimepicker from "vue2-timepicker"
import { helpers } from '~/helpers/general'

export default {

  components: {
    Datepicker,
    VueTimepicker,
  },

  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    bookings: [],
    state: {
      disabledDates: {
        to: new Date(), // Disable all dates up to specific date
        dates: [],
        days: [6, 0]
      }
    },
    form: new Form({
      booking_date: '',
      booking_time: '09:00',
      reserve_type: null
    }),
    hourRange: [[9, 17]],
    disableTime: true,
    reservedDateOrTime: null
  }),

  computed: {
    disabledBookingDates () {
      return this.$store.state.disabledBookingDates
    },
    ...mapGetters({ user: 'auth/user' }),
    showTime () {
      return this.form.reserve_type === 'time'
    },
    showFields () {
      return this.form.reserve_type === 'time' || this.form.reserve_type === 'day'
    }
  },

  mounted () {
    console.log('user', this.user)
  },
  created () {
    this.getBookings()
    this.$store.dispatch('booking/fetchDisabledBookingDates').then((r) => this.state.disabledDates.dates = r)
  },

  methods: {
    async getBookings () {
      const { data } = await axios.get('api/bookings').catch((e) => this.$toastr.e('Error encountered ' + e, 'Error'))
      this.bookings = data

    },
    async manageBookings () {
      this.form.booking_date = this.formatDate(this.form.booking_date)
      this.form.post('api/bookings/admin/reserve')
        .then((data) => this.$toastr.s('Date reserved', 'Success'))
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
<style>
table {
  border-spacing: 10px;
  border-collapse: separate;
}

td, th {
  padding: 10px;
}

</style>
