import moment from 'moment';

export default {
    tweetsSortedByCreatedDate: state => Object.values(state.tweets).sort(
        (a, b) => (
            moment(b.created) - moment(a.created)
        )
    ),

    tweetsSortedByCreatedDateDown: state => Object.values(state.tweets).sort(
        (a, b) => (
            moment(a.created) - moment(b.created)
        )

    ),

    tweetsSortedBylikesCount: state => Object.values(state.tweets).sort(
        (a, b) => (
            b.likesCount - a.likesCount
        )
    ),


    getTweetById: state => id => state.tweets[id],

    isTweetOwner: (state, getters) => (tweetId, userId) => getters.getTweetById(tweetId).author.id === userId,

    tweetIsLikedByUser: (state, getters) => (tweetId, userId) => getters
        .getTweetById(tweetId)
        .likes
        .find(like => like.userId === userId) !== undefined,

        isCardPriview: state => state.inCardPriview
};
