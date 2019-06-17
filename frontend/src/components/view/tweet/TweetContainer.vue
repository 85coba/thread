<template>
  <div v-if="tweet">
    <article class="media box tweet">
      <figure class="media-left">
        <router-link
          v-if="tweet.author.avatar"
          class="image is-64x64 is-square"
          :to="{ name: 'user-page', params: { id: tweet.author.id } }"
        >
          <img class="is-rounded fit" :src="tweet.author.avatar" alt="Author avatar">
        </router-link>

        <router-link v-else :to="{ name: 'user-page', params: { id: tweet.author.id } }">
          <DefaultAvatar class="image is-64x64" :user="tweet.author"/>
        </router-link>
      </figure>

      <div class="media-content">
        <div class="columns">
          <div class="column">
            <div class="content">
              <p class="tweet-text">
                <strong>{{ tweet.author.firstName }} {{ tweet.author.lastName }}</strong>
                <br>
                <small class="has-text-grey">{{ tweet.created | createdDate }}</small>
                <br>
                {{ tweet.text }}
              </p>
              <figure v-if="tweet.imageUrl" class="image is-3by1 tweet-image">
                <img :src="tweet.imageUrl" alt="Tweet image" @click="showImageModal">
              </figure>
              <nav class="level is-mobile">
                <div class="level-left">
                  <b-tooltip label="Comments" animated>
                    <a class="level-item auto-cursor">
                      <span
                        class="icon is-medium has-text-info"
                        :class="{
                                                    'has-text-danger': tweetIsCommentedByUser(tweet.id, user.id)
                                                }"
                      >
                        <font-awesome-icon icon="comments"/>
                      </span>
                      {{ tweet.commentsCount }}
                    </a>
                  </b-tooltip>

                  <b-tooltip label="Like" animated>
                    <a class="level-item" @click="onLikeOrDislikeTweet">
                      <span
                        class="icon is-medium has-text-info"
                        :class="{
                                                    'has-text-danger': tweetIsLikedByUser(tweet.id, user.id)
                                                }"
                      >
                        <font-awesome-icon icon="heart"/>
                      </span>
                    </a>
                  </b-tooltip>
                  <a class="level-item" @click="onShowLikers()">{{ tweet.likesCount }}</a>
                </div>
              </nav>
            </div>
          </div>

          <div class="column is-narrow is-12-mobile">
            <div v-if="isTweetOwner(tweet.id, user.id)" class="buttons">
              <b-button type="is-warning" @click="onEditTweet">Edit</b-button>
              <b-button type="is-danger" @click="onDeleteTweet">Delete</b-button>
            </div>
            <b-button
              type="is-link"
              icon-left="share"
              icon-pack="fas"
              v-clipboard="currentUrl"
              @success="onLinkCopyed"
            >Copy link</b-button>
          </div>
        </div>
        <transition-group name="slide-prev" tag="div">
          <template v-for="comment in comments">
            <Comment :key="comment.id" :comment="comment" :user="user" :tweet="tweet"/>
          </template>
        </transition-group>
        <infinite-loading @infinite="infiniteHandler">
          <div slot="no-more"/>
          <div slot="no-results"/>
          <div slot="spinner"/>
        </infinite-loading>

        <NewCommentForm :tweet-id="tweet.id"/>
      </div>
    </article>

    <b-modal :active.sync="isImageModalActive">
      <p class="image is-4by3">
        <img class="fit" :src="tweet.imageUrl">
      </p>
    </b-modal>

    <b-modal :active.sync="isEditTweetModalActive" has-modal-card>
      <EditTweetForm :tweet="tweet"/>
    </b-modal>

    <b-modal :active.sync="isShowLikers" has-modal-card>
      <ShowLikers :id="tweet.id" :type="'tweet'"/>
    </b-modal>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import Comment from "./Comment.vue";
import NewCommentForm from "./NewCommentForm.vue";
import EditTweetForm from "./EditTweetForm.vue";
import ShowLikers from "./ShowLikers.vue";
import DefaultAvatar from "../../common/DefaultAvatar.vue";
import showStatusToast from "../../mixin/showStatusToast";
import InfiniteLoading from "vue-infinite-loading";
import { ADD_COMMENT } from "@/store/modules/comment/mutationTypes";
import { INCREMENT_COMMENTS_COUNT } from "@/store/modules/tweet/mutationTypes";
import { pusher } from "@/services/Pusher";

export default {
  name: "TweetContainer",

  components: {
    Comment,
    NewCommentForm,
    EditTweetForm,
    DefaultAvatar,
    ShowLikers,
    InfiniteLoading
  },

  mixins: [showStatusToast],

  data: () => ({
    isEditTweetModalActive: false,
    isImageModalActive: false,
    isShowLikers: false,
    page: 1
  }),

  async created() {
    try {
      await this.fetchTweetById(this.$route.params.id);
      await this.fetchCommentsPaginator({
        tweetId: this.tweet.id,
        params: {
          page: 1
        }
      });
      this.page = this.page + 1;
    } catch (error) {
      console.error(error.message);
    };

    const channel = pusher.subscribe("private-comments");

    channel.bind("comment.added", data => {
      commit(`comment/${ADD_COMMENT}`, dtata.comment);
      commit(`tweet/${INCREMENT_COMMENTS_COUNT}`, this.tweet.id, { root: true });
    });
  },

  beforeDestroy() {
    pusher.unsubscribe("private-comments");
  },

  computed: {
    ...mapGetters("auth", {
      user: "getAuthenticatedUser"
    }),

    ...mapGetters("tweet", [
      "getTweetById",
      "isTweetOwner",
      "tweetIsLikedByUser"
    ]),

    ...mapGetters("comment", [
      "tweetIsCommentedByUser",
      "getCommentsByTweetId"
    ]),

    comments() {
      return this.getCommentsByTweetId(this.tweet.id);
    },

    tweet() {
      return this.getTweetById(this.$route.params.id);
    },

    currentUrl() {
      return window.location.href;
    }
  },

  methods: {
    ...mapActions("tweet", [
      "fetchTweetById",
      "deleteTweet",
      "likeOrDislikeTweet"
    ]),

    ...mapActions("comment", ["fetchComments", "fetchCommentsPaginator"]),

    onShowLikers() {
      this.isShowLikers = true;
    },

    onEditTweet() {
      this.isEditTweetModalActive = true;
    },

    onLinkCopyed() {
      this.showSuccessMessage("Link on this Tweet copyed to clipboard!");
    },

    onDeleteTweet() {
      this.$dialog.confirm({
        title: "Deleting tweet",
        message:
          "Are you sure you want to <b>delete</b> your tweet? This action cannot be undone.",
        confirmText: "Delete Tweet",
        type: "is-danger",

        onConfirm: async () => {
          try {
            await this.deleteTweet(this.tweet.id);

            this.showSuccessMessage("Tweet deleted!");

            this.$router.push({ name: "feed" });
          } catch {
            this.showErrorMessage("Unable to delete tweet!");
          }
        }
      });
    },

    showImageModal() {
      this.isImageModalActive = true;
    },

    async onLikeOrDislikeTweet() {
      try {
        await this.likeOrDislikeTweet({
          id: this.tweet.id,
          userId: this.user.id
        });
      } catch (error) {
        console.error(error.message);
      }
    },
    async infiniteHandler($state) {
      try {
        const comments = await this.fetchCommentsPaginator({
          tweetId: this.tweet.id,
          params: {
            page: this.page
          }
        });
        if (comments.length) {
          this.page += 1;
          $state.loaded();
        } else {
          $state.complete();
        }
      } catch (error) {
        this.showErrorMessage(error.message);
        $state.complete();
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import "~bulma/sass/utilities/initial-variables";
@import "../../../styles/common";

.tweet-image {
  margin: 12px 0 0 0;

  img {
    width: auto;
    cursor: pointer;
  }
}

.buttons {
  .button {
    @media screen and (max-width: $tablet) {
      font-size: 0.8rem;
    }
  }
}

.tweet-text {
  max-width: 100%;
}

.activity {
  margin-bottom: 16px;
}

.content {
  figure {
    margin-top: 0;
    margin-bottom: 0.75rem;
  }
}

.column {
  padding-bottom: 0;
}
</style>
