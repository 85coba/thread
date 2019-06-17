<template>
  <div class="feed-container">
    <div class="navigation">
      <div class="level">
        <div class="level-left">
          <b-button
            class="btn-add-tweet"
            rounded
            size="is-medium"
            type="is-danger"
            icon-left="twitter"
            icon-pack="fab"
            @click="onAddTweetClick"
          >Tweet :)</b-button>
        </div>
        <div class="level-right">
          <b-button class="button is-primary" @click="onChangeView" rounded>
              <span>Change view</span>
          </b-button>
          <b-dropdown aria-role="list">
            <b-button class="button is-primary" slot="trigger" rounded>
              <span>Sort by</span>
            </b-button>

            <b-dropdown-item aria-role="listitem" @click="onChangeSort('Date')">Sort by date</b-dropdown-item>
            <b-dropdown-item aria-role="listitem" @click="onChangeSort('DateDown')">Sort by date des</b-dropdown-item>
            <b-dropdown-item aria-role="listitem" @click="onChangeSort('Like')">Sort by likes</b-dropdown-item>
          </b-dropdown>
        </div>
      </div>
    </div>

    <TweetPreviewList :tweets="tweets" @infinite="infiniteHandler"/>

    <b-modal :active.sync="isNewTweetModalActive" has-modal-card>
      <NewTweetForm/>
    </b-modal>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import TweetPreviewList from "../../common/TweetPreviewList.vue";
import NewTweetForm from "./NewTweetForm.vue";
import { pusher } from "@/services/Pusher";
import { SET_TWEET, SET_PRIVIEW } from "@/store/modules/tweet/mutationTypes";
import showStatusToast from "@/components/mixin/showStatusToast";

export default {
  name: "FeedContainer",

  mixins: [showStatusToast],

  components: {
    TweetPreviewList,
    NewTweetForm
  },

  data: () => ({
    isNewTweetModalActive: false,
    page: 1,
    SortBy: "Date"
  }),

  async created() {
    try {
      await this.fetchTweets({
        page: 1
      });
    } catch (error) {
      this.showErrorMessage(error.message);
    }

    const channel = pusher.subscribe("private-tweets");

    channel.bind("tweet.added", data => {
      this.$store.commit(`tweet/${SET_TWEET}`, data.tweet);
    });
  },

  beforeDestroy() {
    pusher.unsubscribe("private-tweets");
  },

  computed: {
    ...mapGetters("tweet", [
      "tweetsSortedByCreatedDate",
      "tweetsSortedByCreatedDateDown",
      "tweetsSortedBylikesCount",
    ]),
    tweets: function() {
      switch (this.SortBy) {
        case "Date":
          return this.tweetsSortedByCreatedDate;
        case "Like":
          return this.tweetsSortedBylikesCount;
        case "DateDown":
          return this.tweetsSortedByCreatedDateDown;
      }
    }
  },

  methods: {
    ...mapActions("tweet", ["fetchTweets"]),

    onChangeView() {
      
      this.$store.commit(`tweet/${SET_PRIVIEW}`);
    },

    onChangeSort(sortBy) {
      this.SortBy = sortBy;
    },

    onAddTweetClick() {
      this.showAddTweetModal();
    },

    showAddTweetModal() {
      this.isNewTweetModalActive = true;
    },

    async infiniteHandler($state) {
      try {
        const tweets = await this.fetchTweets({ page: this.page + 1 });

        if (tweets.length) {
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

<style scoped lang="scss">
@import "~bulma/sass/utilities/initial-variables";

.navigation {
  padding: 10px 0;
  margin-bottom: 20px;
}

.modal-card {
  border-radius: 6px;
}

.btn-add-tweet {
  transition: 0.2s ease-out all;
  box-shadow: 1px 5px 5px 0 #00000020;

  &:hover {
    box-shadow: 1px 1px 0 0 #00000020;
  }

  @media screen and (max-width: $tablet) {
    font-size: 1rem;
  }
}
</style>
