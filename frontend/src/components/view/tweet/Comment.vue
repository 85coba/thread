<template>
  <article class="media">
    <figure class="media-left">
      <router-link
        v-if="comment.author.avatar"
        class="image is-48x48 is-square"
        :to="{ name: 'user-page', params: { id: comment.author.id } }"
      >
        <img class="is-rounded fit" :src="comment.author.avatar">
      </router-link>

      <router-link v-else :to="{ name: 'user-page', params: { id: comment.author.id } }">
        <DefaultAvatar class="image is-48x48" :user="comment.author"/>
      </router-link>
    </figure>
    <div class="media-content">
      <div class="columns">
        <div class="column">
          <div class="content">
            <p>
              <strong>{{ comment.author.firstName }} {{ comment.author.lastName }}</strong>
              <br>
              {{ comment.body }}
              <br>
            </p>
            <figure v-if="comment.imageUrl" class="image is-3by1 comment-image">
              <img :src="comment.imageUrl" alt="Comment image">
            </figure>

            <small class="has-text-grey">{{ comment.created | createdDate }}</small>
            <div class="level-left">
              <b-tooltip label="Like" animated>
                <a class="level-item" @click="onLikeOrDislikeComment">
                  <span
                    class="icon is-medium has-text-info"
                    :class="{
                              'has-text-danger': commentIsLikedByUser(comment.id, user.id)
                          }"
                  >
                    <font-awesome-icon icon="heart"/>
                  </span>
                </a>
              </b-tooltip>
              <a class="level-item" @click="onShowLikers">{{ comment.likesCount }}</a>
            </div>
            <EditCommentForm
              v-if="isEditCommentActive"
              :commentId="comment.id"
              :body="comment.body"
            />
          </div>
        </div>

        <div v-if="isCommentOwner(comment.id, user.id)" class="column is-narrow is-12-mobile">
          <div class="buttons">
            <b-button type="is-warning" @click="onEditComment">Edit</b-button>
            <b-button type="is-danger" @click="onDeleteComment">Delete</b-button>
          </div>
        </div>
      </div>
      <b-modal :active.sync="isShowLikers" has-modal-card>
        <ShowLikers :id="comment.id" :type="'comment'"/>
      </b-modal>
    </div>
  </article>
</template>

<script>
import EditCommentForm from "./EditCommentForm.vue";
import DefaultAvatar from "../../common/DefaultAvatar.vue";
import { mapGetters, mapActions } from "vuex";
import showStatusToast from "../../mixin/showStatusToast";
import ShowLikers from "./ShowLikers.vue";

export default {
  name: "Comment",

  components: {
    DefaultAvatar,
    EditCommentForm,
    ShowLikers
  },

  mixins: [showStatusToast],

  data: () => ({
    isEditCommentActive: false,
    isShowLikers: false
  }),

  props: {
    comment: {
      type: Object,
      required: true
    },
    user: {
      type: Object,
      required: true
    },
    tweet: {
      type: Object,
      required: true
    }
  },

  computed: {
    ...mapGetters("comment", ["isCommentOwner", "commentIsLikedByUser"])
  },

  methods: {
    ...mapActions("comment", ["deleteComment", "likeOrDislikeComment"]),

    async onLikeOrDislikeComment() {
      try {
        await this.likeOrDislikeComment({
          id: this.comment.id,
          userId: this.user.id
        });
      } catch (error) {
        console.error(error.message);
      }
    },

    onShowLikers() {
      this.isShowLikers = true;
    },

    onEditComment() {
      this.isEditCommentActive =
        this.isEditCommentActive === false ? true : false;
    },

    onDeleteComment() {
      this.$dialog.confirm({
        title: "Deleting comment",
        message:
          "Are you sure you want to <b>delete</b> your comment? This action cannot be undone.",
        confirmText: "Delete Comment",
        type: "is-danger",

        onConfirm: async () => {
          try {
            await this.deleteComment(this.comment.id);

            this.showSuccessMessage("Comment deleted!");
          } catch {
            this.showErrorMessage("Unable to delete comment!");
          }
        }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
nav {
  margin-left: -8px;
}

.content {
  p {
    margin-bottom: 0;
  }
}
.comment-image {
  margin: 12px 0 0 0;

  img {
    width: auto;
    cursor: pointer;
  }
}
</style>
