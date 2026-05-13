import { createStore } from 'vuex';
import auth from './auth';
import front from './front';
import hrm from './hrm';

export default createStore({
    modules: {
        auth,
        front,
        hrm
    }
})
