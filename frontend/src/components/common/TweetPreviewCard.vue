<template>
  <div class="tweet box" @click="$emit('click', tweet)">
    <div class="card">
      <div class="card-image">
        <figure v-if="tweet.imageUrl" class="image is-3by1 tweet-image">
          <img :src="tweet.imageUrl" alt="Tweet image">
        </figure>
      </div>
      <div class="card-content">
        <div class="media">
          <div class="media-left">
            <figure class="image is-48x48">
              <router-link
                v-if="tweet.author.avatar"
                class="image is-48x48 is-square"
                :to="{ name: 'user-page', params: { id: tweet.author.id } }"
              >
                <img class="is-rounded fit" :src="tweet.author.avatar" alt="Author avatar">
              </router-link>

              <router-link v-else :to="{ name: 'user-page', params: { id: tweet.author.id } }">
                <DefaultAvatar class="image is-48x48" :user="tweet.author"/>
              </router-link>
            </figure>
          </div>
          <div class="media-content">
            <p class="title is-4">{{ tweet.author.firstName }} {{ tweet.author.lastName }}</p>
            <p class="subtitle is-6">@{{ tweet.author.nickname }}</p>
          </div>
        </div>

        <div class="content">
          {{ tweet.text }}        
        </div>
        <small class="created">{{ tweet.created | createdDate }}</small>
        <footer class="card-footer">
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
          <a class="level-item auto-cursor">
            <span
              class="icon is-medium has-text-info"
              :class="{
                                                    'has-text-danger': tweetIsLikedByUser(tweet.id, user.id)
                                                }"
            >
              <font-awesome-icon icon="heart"/>
            </span>
            {{ tweet.likesCount }}
          </a>
        </footer>
      </div>
    </div>
  </div>
</template>

<script>
import DefaultAvatar from "./DefaultAvatar.vue";
import { mapGetters, mapActions } from "vuex";
export default {
  name: "TweetPreview",

  components: {
    DefaultAvatar
  },

  props: {
    tweet: {
      type: Object,
      required: true
    },
    user: {
      type: Object,
      required: true
    }
  },

  computed: {
    ...mapGetters("tweet", ["tweetIsLikedByUser"]),
    ...mapGetters("comment", ["tweetIsCommentedByUser"])
  },
  methods: {
    ...mapActions("comment", ["fetchComments"])
  }
};
</script>

<style lang="scss" scoped>
@import "../../styles/common";

.tweet {
  cursor: pointer;
  padding: 15px;
  border-radius: 5px;
  box-shadow: 5px 5px 5px 0 #00000020;
  transition: 0.2s ease-out all;

  &:hover {
    box-shadow: 1px 1px 0 0 #00000020;
  }

  &-image {
    margin: 12px 0 0 0;

    img {
      width: auto;
    }
  }

  .nickname {
    margin-left: 5px;
  }

  .created {
    margin-left: 5px;
  }
}
</style>
