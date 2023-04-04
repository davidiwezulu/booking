import axios from 'axios'
import * as types from '../mutation-types'
import { helpers } from '~/helpers/general'

// state
export const state = {
  disabledBookingDates: null,
  availableHours: null
}

// getters
export const getters = {
  disabledBookingDates: state => state.disabledBookingDates
}

// mutations
export const mutations = {
  [types.DISABLED_BOOKING_DATE] (state, { disabledBookingDates }) {
    state.disabledBookingDates = disabledBookingDates
  },
  [types.AVAILABLE_HOURS] (state, { availableHours }) {
    state.availableHours = availableHours
  }
}

// actions
export const actions = {
  async fetchDisabledBookingDates ({ commit }) {
    const dates = []
    try {
      const { data } = await axios.get('api/bookings/disabled/dates')
      data.forEach(function (item) {
        const date = item.split('-')
        dates.push(new Date(date[0], (parseInt(date[1]) - 1), date[2]))
      })
      commit(types.DISABLED_BOOKING_DATE, { disabledBookingDates: dates })
    } catch (e) {}
    return dates
  },

  async fetchAvailableHours ({ commit }, date) {
    let availableHours = []
    try {
      const { data } = await axios.get('api/bookings/available/hours/' + helpers.formatDate(date))
      availableHours = Object.values(data)
      commit(types.AVAILABLE_HOURS, { availableHours: availableHours })
    } catch (e) {}

    return availableHours
  }

}
