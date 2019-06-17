<template>
  <article class="media">
    <figure class="media-left">
      <p class="image is-48x48 is-square" v-if="user.avatar">
        <img class="is-rounded fit" :src="user.avatar">
      </p>
      <DefaultAvatar v-else class="image is-48x48" :user="user"/>
    </figure>
    <div class="media-content">
      <div class="field">
        <p class="control">
          <b-input type="textarea" v-model="text" :placeholder="body" 
                                  @keyup.ctrl.exact.enter="onPostComment" />
        </p>
      </div>
      <div class="field">
        <p class="control">
          <button
            class="button is-primary comment-btn"
            
            @click="onPostComment"
          >Post comment</button>

          <b-field class="file comment-btn">
            <b-upload v-model="image">
              <a class="button is-primary">
                <b-icon pack="fa" icon="upload"/>
                <span>Upload image</span>
              </a>
            </b-upload>
            <span class="file-name" v-if="image">{{ image.name }}</span>
          </b-field>
        </p>
      </div>
    </div>
  </article>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DefaultAvatar from "@/components/common/DefaultAvatar.vue";

export default {
  name: "EditCommentForm",

  components: {
    DefaultAvatar
  },

  props: ['commentId', 'body'],

  data: () => ({
    image: null,
    text: ''
  }),

  computed: {
    ...mapGetters("auth", {
      user: "getAuthenticatedUser"
    }),
  },

  methods: {
    ...mapActions("comment", [
      "addComment",
      "uploadCommentImage",
      "editComment"
    ]),

    async onPostComment() {
      const comment = await this.editComment({id: this.commentId, body: this.text
      });

      if (this.image === null) {
        return;
      }

      await this.uploadCommentImage({
        id: comment.id,
        imageFile: this.image
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.textarea {
  min-height: 60px;
}

.comment-btn {
  display: inline;
}
</style>
