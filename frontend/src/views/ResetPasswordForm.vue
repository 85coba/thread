<template>
  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="columns is-mobile is-centered">
          <div class="column is-three-quarters-mobile is-two-thirds-tablet is-one-third-desktop">
            <div class="box shadow-box">
              <p class="subtitle">Enter new password</p>

              <form class="form" @submit.prevent novalidate="true">
                <b-field label="Email">
                  <b-input v-model="email" name="email" autofocus/>
                </b-field>

                <b-field label="Password">
                  <b-input type="password" v-model="password"/>
                </b-field>

                <b-field label="Confirm Password">
                  <b-input type="password" v-model="password_confirmation"/>
                </b-field>

                <div class="login-footer has-text-centered">
                  <button
                    type="button"
                    class="button is-primary is-rounded"
                    @click="resetPassword"
                  >Set</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
import { mapActions } from "vuex";
import showStatusToast from "../components/mixin/showStatusToast";

export default {
  name: "ResetPasswordForm",

  mixins: [showStatusToast],

  data() {
    return {
      token: null,
      email: null,
      password: null,
      password_confirmation: null,
      has_error: false
    };
  },
  methods: {
    ...mapActions("auth", ["actionResetPassword"]),

    resetPassword() {
      let payload = {
        token: this.$route.params.token,
        email: this.email,
        password: this.password,
        password_confirmation: this.password_confirmation
      };
      const message = this.actionResetPassword(payload);
      this.showSuccessMessage('Done');
      this.$router.push({ name: "auth.signIn" });
    }
  }
};
</script>