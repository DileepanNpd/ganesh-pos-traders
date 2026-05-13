const IS_SELF_CLOCKING = 'hrm_is_self_clocking';
const SHOW_CLOCK_IN_BUTTON = 'hrm_show_clock_in_button';
const SHOW_CLOCK_OUT_BUTTON = 'hrm_show_clock_out_button';
const CLOCK_IN_DATE_TIME = 'hrm_clock_in_date_time';
const CLOCK_OUT_DATE_TIME = 'hrm_clock_in_date_time';

export default {
    namespaced: true,
    state() {
        return {
            isSelfClocking: window.localStorage.getItem(IS_SELF_CLOCKING) || false,
            showClockInButton: window.localStorage.getItem(SHOW_CLOCK_IN_BUTTON) || false,
            showClockOutButton: window.localStorage.getItem(SHOW_CLOCK_OUT_BUTTON) || false,
            clockInDateTime: window.localStorage.getItem(CLOCK_IN_DATE_TIME) || undefined,
            clockOutDateTime: window.localStorage.getItem(CLOCK_OUT_DATE_TIME) || undefined,
        }
    },

    mutations: {
        updateSelfClocking(state, isSelfClocking) {
            state.isSelfClocking = isSelfClocking;
            window.localStorage.setItem(IS_SELF_CLOCKING, isSelfClocking);
        },
        updateShowClockInButton(state, showClockInButton) {
            state.showClockInButton = showClockInButton ? 1 : 0;
            window.localStorage.setItem(SHOW_CLOCK_IN_BUTTON, showClockInButton);
        },
        updateShowClockOutButton(state, showClockOutButton) {
            state.showClockOutButton = showClockOutButton ? 1 : 0;
            window.localStorage.setItem(SHOW_CLOCK_OUT_BUTTON, showClockOutButton);
        },
        updateClockInDateTime(state, clockInDateTime) {
            state.clockInDateTime = clockInDateTime;
            window.localStorage.setItem(CLOCK_IN_DATE_TIME, clockInDateTime);
        },
        updateClockOutDateTime(state, clockOutDateTime) {
            state.clockOutDateTime = clockOutDateTime;
            window.localStorage.setItem(CLOCK_OUT_DATE_TIME, clockOutDateTime);
        },
    },

    actions: {
        updateSelfClocking(context) {
            axiosAdmin.get("/hrm/today-attendance-details").then((response) => {
                context.commit('updateSelfClocking', response.data.self_clocking);
                context.commit('updateShowClockInButton', response.data.show_clock_in_button);
                context.commit('updateShowClockOutButton', response.data.show_clock_out_button);
                context.commit('updateClockInDateTime', response.data.clock_in_date_time);
                context.commit('updateClockOutDateTime', response.data.clock_out_date_time);
            });
        },
    },

    getters: {

    }
}
