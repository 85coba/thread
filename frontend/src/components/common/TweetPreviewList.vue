<template>
  <div class="tweets-container">
    <transition-group name="slide-prev" tag="div">
      <template v-for="tweet in tweets">
        <TweetPreview v-if="!isCardPriview" :key="tweet.id" 
            :tweet="tweet" 
            :user="user"
            :comments="comments"
            @click="onTweetClick"/>
        
        <TweetPreviewCard v-else :key="tweet.id" 
            :tweet="tweet" 
            :user="user" 
            @click="onTweetClick"/>
      </template>
    </transition-group>
    <infinite-loading @infinite="infiniteHandler">
      <div slot="no-more"/>
      <div slot="no-results"/>
      <div slot="spinner"/>
    </infinite-loading>
  </div>
</template>

<script>
import InfiniteLoading from "vue-infinite-loading";
import TweetPreview from "./TweetPreview.vue";
import TweetPreviewCard from "./TweetPreviewCard";
import { mapGetters, mapActions } from "vuex";

export default {
  name: "TweetPreviewList",

  props: {
    tweets: {
      type: Array,
      required: true
    },
  },

  components: {
    TweetPreview,
    InfiniteLoading,
    TweetPreviewCard
  },

  async created() { 
        await this.fetchAuthenticatedUser().then(() => {
        this.fetchCommentsAll(this.user.id);
      }); 
  },

  computed: {
    ...mapGetters("auth",{
      user:"getAuthenticatedUser"
    }),
    
    ...mapGetters("comment",
    {comments: "getCommentsSortedByCreatedDateAsc"
    }),

    ...mapGetters("tweet", {
      isCardPriview : "isCardPriview"
    })
  },

  methods: {
    ...mapActions("comment", ["fetchCommentsAll"]),
    ...mapActions("auth", ["fetchAuthenticatedUser"]),

    onTweetClick(tweet) {
      this.$router.push({ name: "tweet-page", params: { id: tweet.id } });
    },

    infiniteHandler($state) {
      this.$emit("infinite", $state);
    }
  }
};
</script>

<style scoped lang="scss">
.tweets-container {
  padding-bottom: 20px;
}
</style>
