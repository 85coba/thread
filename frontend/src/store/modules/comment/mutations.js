import { 
    SET_COMMENTS, 
    ADD_COMMENT, 
    SET_COMMENT_IMAGE, 
    DELETE_COMMENT,
    LIKE_COMMENT,
    DISLIKE_COMMENT,
    UPDATE_COMMENTS
} from './mutationTypes';
import { commentMapper } from '@/services/Normalizer';

export default {
    [SET_COMMENTS]: (state, comments) => {
        let commentsByIdMap = {...state.comments};
        comments.forEach(comment => {
            commentsByIdMap = {
                ...commentsByIdMap,
                [comment.id]: commentMapper(comment)
            };
        });

        state.comments = commentsByIdMap;
    },

    [UPDATE_COMMENTS]: (state, comments) => {

        let commentsByIdMap = {...state.comments};
        console.log(commentsByIdMap);
        comments.forEach(comment => {
            commentsByIdMap = {
                ...commentsByIdMap,
                [comment.id]: commentMapper(comment)
            };
        });
    },

    [ADD_COMMENT]: (state, comment) => {
        state.comments = {
            ...state.comments,
            [comment.id]: commentMapper(comment)
        };
    },

    [SET_COMMENT_IMAGE]: (state, { id, imageUrl }) => {
        state.comments[id].imageUrl = imageUrl;
    },

    [DELETE_COMMENT]: (state, id) => {
        delete state.comments[id];
        state.comments = { ...state.comments}
    },

    [LIKE_COMMENT]: (state, { id, userId}) => {
        state.comments[id].likesCount++;

        state.comments[id].likes.push({ userId });
    },

    [DISLIKE_COMMENT]: (state, { id, userId }) => {
        state.comments[id].likesCount--;

        state.comments[id].likes = state.comments[id].likes.filter(like => like.userId !== userId);
    }

};
