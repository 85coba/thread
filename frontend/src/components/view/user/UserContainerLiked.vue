<template>
  <div class="user-container">
    <TweetPreviewList :tweets="tweets" @infinite="infiniteHandler"/>
    <NoContent :show="noContent" title="No tweets yet :)"/>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import TweetPreviewList from "@/components/common/TweetPreviewList.vue";
import NoContent from "@/components/common/NoContent.vue";
import showStatusToast from "@/components/mixin/showStatusToast";

export default {
  name: "UserContainerLiked",

  mixins: [showStatusToast],

  components: {
    TweetPreviewList,
    NoContent
  },

  data: () => ({
    tweets: [],
    page: 1,
    noContent: false
  }),

  async created() {
    try {
      this.tweets = await this.fetchLikedTweetsByUserId({
        userId: this.$route.params.id,
        params: {
          page: 1
        }
      });

      if (!this.tweets.length) {
        this.noContent = true;
      }
    } catch (error) {
      this.showErrorMessage(error.message);
    }
  },

  methods: {
    ...mapActions("tweet", ["fetchLikedTweetsByUserId"]),

    async infiniteHandler($state) {
      try {
        const tweets = await this.fetchLikedTweetsByUserId({
          userId: this.$route.params.id,
          params: {
            page: this.page + 1
          }
        });
        if (tweets.length) {
          this.tweets.push(...tweets);
          this.page += 1;
          $state.loaded();
        } else {
          $state.complete();
        }

        if (tweets.length) {
          this.tweets.push(...tweets);
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

<style scoped>
</style>
