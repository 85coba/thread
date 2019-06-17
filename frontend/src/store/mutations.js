import { SET_LOADING } from './mutationTypes';
import { SET_PRIVIEW } from './mutationTypes';

export default {
    [SET_LOADING]: (state, isLoading = true) => {
        state.isLoading = isLoading;
    },
};
