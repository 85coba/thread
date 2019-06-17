<template>
<section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="columns is-mobile is-centered">
          <div class="column is-three-quarters-mobile is-two-thirds-tablet is-one-third-desktop">
            <div class="box shadow-box">
              <p class="subtitle">Send reset password mail</p>

              <form class="form" @submit.prevent novalidate="true">
                <b-field label="Email">
                  <b-input v-model="email" name="email" autofocus/>
                </b-field>

                <div class="login-footer has-text-centered">
                  <button
                    type="button"
                    class="button is-primary is-rounded"
                    @click="onSubmit(email)"
                  >Send</button>
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
import { mapActions } from 'vuex';
import showStatusToast from '../components/mixin/showStatusToast';

export default {
    name: 'ForgotPassword',

    mixins: [showStatusToast],

    data() {
      return {
        email: null,
      }
    },
    methods: {
        ...mapActions('auth', [
            'sendRequestResetPassword',
        ]),

        onSubmit(email) {
            this.sendRequestResetPassword(email);
            this.$router.push({ name: "auth.signIn" });
        }
        }
    }
</script>