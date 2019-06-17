import moment from 'moment';

export default {
    getCommentsSortedByCreatedDateAsc: state => Object
        .values(state.comments)
        .sort(
            (a, b) => (
                moment(a.created).isBefore(moment(b.created)) ? -1 : 1
            )
        ),

    getCommentById: state => id => state.comments[id],

    getCommentsByTweetId: (state, getters) => tweetId => getters
        .getCommentsSortedByCreatedDateAsc
        .filter(comment => comment.tweetId === tweetId),

    isCommentOwner: (state, getters) => (commentId, userId) => getters.getCommentById(commentId).author.id === userId,

    tweetIsCommentedByUser: (state, getters) => (tweetId, userId) => getters
        .getCommentsByTweetId(tweetId)
        .find(comment => comment.authorId === userId),
    
    commentIsLikedByUser: (state, getters) => (commentId, userId) => getters
        .getCommentById(commentId)
        .likes
        .find(like => like.userId === userId) !== undefined,
};
