<template>
  <v-row
    align="center"
    justify="center"
    class="fondo animate__animated animate__rubberBand"
  >
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-col sm="5" md="4" lg="3">
      <v-list-item style="background: #444140;">
        <v-list-item-content class="text-center" style="color: white;">
          <img :src="getLogo" width="5px" />
          <v-list-item-title class="headline">
            Inicio de Sesión
          </v-list-item-title>
          <v-list-item-subtitle style="color: white;">
            Sistema
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-card class="elevation-12">
        <v-card-text>
          <v-form>
            <v-text-field
              type="text"
              v-model="credentials.cui"
              name="número de dpi"
              prepend-icon="mdi-account"
              placeholder="número de dpi"
              v-validate="'required|numeric'"
              :class="{
                input: true,
                'has-errors': errors.has('número de dpi'),
              }"
            ></v-text-field>
            <FormError
              :attribute_name="'número de dpi'"
              :errors_form="errors"
            ></FormError>

            <v-text-field
              @keypress.enter="beforeLogin"
              type="password"
              name="contraseña"
              prepend-icon="mdi-lock"
              v-model="credentials.password"
              placeholder="Password"
              v-validate="'required'"
              :class="{ input: true, 'has-errors': errors.has('contraseña') }"
            ></v-text-field>
            <FormError
              :attribute_name="'contraseña'"
              :errors_form="errors"
            ></FormError>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="beforeLogin" x-large color="primary">INGRESAR</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<style scoped>
.fondo {
  height: 100%;
  background-repeat: no-repeat;
  background: url(https://designshack.net/wp-content/uploads/tips-for-using-background-textures-in-web-design.jpg)
    no-repeat center center fixed;
  background-size: 100% 100%;
}
</style>

<script>
import FormError from '../shared/FormError'
import auth from '../../auth'
export default {
  name: 'Login',
  components: {
    FormError,
  },

  data() {
    return {
      loading: false,
      credentials: {
        cui: '',
        password: '',
      },
    }
  },

  created() {
    let self = this
  },
  methods: {
    login() {
      let self = this
      self.loading = true
      self.$store.state.services.loginService
        .login(self.credentials)
        .then((r) => {
          self.loading = false
          if (self.$store.state.global.captureError(r)) {
            self.loading = false
            return
          }
          self.$store.dispatch('guardarToken', r.data)
          auth.getUser()
          self.$router.push('/')
        })
        .catch((e) => {})
    },

    beforeLogin() {
      let self = this
      self.$validator.validateAll().then((result) => {
        if (result) {
          self.login()
        }
      })
    },
  },

  computed: {
    getLogo() {
      let self = this
      let logo = this.$cookies.get('logo')
        ? this.$cookies.get('logo')
        : 'conectividad_1.jpeg'
      return `${self.$store.state.base_url}img/${logo}`
    },
  },
}
</script>
