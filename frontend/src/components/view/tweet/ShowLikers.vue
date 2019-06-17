<template>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">They liked</p>
    </header>

    <section class="modal-card-body">
      <div v-if="users.length > 0">
        <div v-for="user in users" :key="user.id">
          <article class="media">
            <figure class="media-left">
              <router-link
                v-if="user.avatar"
                class="image is-48x48 is-square"
                :to="{ name: 'user-page', params: { id: user.id } }"
              >
                <img class="is-rounded fit" :src="user.avatar">
              </router-link>

              <router-link v-else :to="{ name: 'user-page', params: { id: user.id } }">
                <DefaultAvatar class="image is-48x48" :user="user"/>
              </router-link>
            </figure>
            <strong>{{ user.firstName }} {{ user.lastName }}</strong>
          </article>
        </div>
      </div>
    </section>

    <footer class="modal-card-foot">
      <b-button type="is-primary" @click="exit">Close</b-button>
    </footer>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import DefaultAvatar from '@/components/common/DefaultAvatar.vue';

export default {
  name: "ShowLikers",

  components: {
        DefaultAvatar,
    },

  props: ["id", "type"],

  data: () => ({
    users: []
  }),

  async mounted() {
    if (this.type === 'tweet') {
      this.users = await this.fetchUsersByLikeTweet(this.id);
    } else if (this.type === 'comment') {
      this.users = await this.fetchUsersByLikeComment(this.id);
    }
    
  },

  methods: {
    ...mapActions("auth", ["fetchUsersByLikeTweet", "fetchUsersByLikeComment"]),

    async exit() {
      this.$parent.close();
    },

    showErrorMessage(msg) {
      this.errorMessage = msg;
    }
  }
};
</script>

<style scoped>
.error {
  padding: 10px 0;
}
</style>
