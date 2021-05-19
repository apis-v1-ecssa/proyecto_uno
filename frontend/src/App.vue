<template>
  <v-app id="inspire">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-navigation-drawer
      v-show="isLogin"
      v-model="drawer"
      v-if="drawer"
      app
      clipped
      width="300"
      class="animate__animated animate__backInLeft animate__delay-1s"
    >
      <v-row justify="space-around">
        <v-col cols="12" sm="12" md="12">
          <v-sheet elevation="10" class="py-4 px-1">
            <v-chip-group
              v-model="seleccionado"
              mandatory
              active-class="primary--text"
            >
              <v-chip
                v-for="tag in tags"
                :key="tag"
                :value="tag"
                @click="cambiar_logo(tag)"
              >
                {{ tag }}
              </v-chip>
            </v-chip-group>
          </v-sheet>
        </v-col>
      </v-row>
      <v-card class="mx-auto" max-width="434" tile>
        <v-img height="100%" :src="getLogo">
          <v-row>
            <v-col>
              <v-list-item dark>
                <v-list-item-content>
                  <v-list-item-title
                    class="title"
                    :style="style"
                    v-text="usuario_nombre"
                  ></v-list-item-title>
                  <v-list-item-subtitle
                    :style="style"
                    v-text="usuario_email"
                  ></v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
        </v-img>
      </v-card>
      <v-divider></v-divider>
      <v-list dense>
        <v-list-item @click="redirect('/')" link>
          <v-list-item-action>
            <v-icon>home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Inicio</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-list-group
          v-for="item in getMenu"
          :key="item.text"
          :prepend-icon="item.icon"
          no-action
        >
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title v-text="item.text"></v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item
            v-for="subItem in item.childrens"
            :key="subItem.path"
            link
            @click="redirect(subItem.path)"
          >
            <v-list-item-icon>
              <v-icon>{{ subItem.icon }}</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="subItem.text"></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar
      v-show="isLogin"
      app
      :clipped-left="$vuetify.breakpoint.lgAndUp"
      dense
      class="animate__animated animate__backInDown animate__delay-2s"
    >
      <v-app-bar-nav-icon @click="mostar"></v-app-bar-nav-icon>

      <v-spacer></v-spacer>
      <v-btn-toggle>
        <v-btn color="warning" @click="ir('')">
          <v-icon>mdi-home</v-icon>
          <span>Principal</span>
        </v-btn>

        <v-btn color="success" v-if="ver('acept')" @click="ir('acept')">
          <v-icon>mdi-history</v-icon>
          <span>Aceptados</span>
        </v-btn>

        <v-btn color="primary" v-if="ver('complete')" @click="ir('complete')">
          <v-icon>mdi-history</v-icon>
          <span>Pendiente de Firma</span>
        </v-btn>

        <v-btn color="error" v-if="ver('cancel')" @click="ir('cancel')">
          <v-icon>mdi-history</v-icon>
          <span>Anulados</span>
        </v-btn>
      </v-btn-toggle>
      <v-spacer></v-spacer>

      <v-menu bottom min-width="200px" rounded offset-y>
        <template v-slot:activator="{ on }">
          <v-btn icon x-large v-on="on">
            <v-avatar color="brown" size="40">
              <img
                :src="usuario_foto"
                v-if="usuario_foto"
                :alt="usuario_nombre"
              />
              <span
                v-else
                class="white--text headline"
                v-text="usuario_inicial"
              ></span>
            </v-avatar>
          </v-btn>
        </template>
        <v-card>
          <v-list-item-content class="justify-center">
            <div class="mx-auto text-center">
              <v-avatar color="brown">
                <img
                  :src="usuario_foto"
                  v-if="usuario_foto"
                  :alt="usuario_nombre"
                />
                <span
                  v-else
                  class="white--text headline"
                  v-text="usuario_inicial"
                ></span>
              </v-avatar>
              <h3 v-text="usuario_nombre"></h3>
              <p class="caption mt-1" v-text="usuario_cui"></p>
              <p class="caption mt-1" v-text="usuario_email"></p>
              <v-divider class="my-3"></v-divider>

              <v-btn depressed rounded text @click="cambiar_password">
                Cambiar Contraseña
              </v-btn>

              <v-divider class="my-3"></v-divider>
              <v-btn small fab outlined color="red" @click="logout">
                <v-icon>exit_to_app</v-icon>
              </v-btn>
            </div>
          </v-list-item-content>
        </v-card>
      </v-menu>
    </v-app-bar>

    <v-main>
      <v-container
        class="fill-height animate__animated animate__backInUp animate__delay-3s"
        fluid
      >
        <router-view></router-view>

        <v-dialog
          v-model="dialog_password"
          width="35%"
          persistent
          color="primary"
        >
          <v-card>
            <v-overlay :value="loading">
              <v-progress-circular
                indeterminate
                size="64"
              ></v-progress-circular>
            </v-overlay>
            <v-card-title>
              <span class="headline">Cambiar contraseña</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12" md="12">
                    <v-text-field
                      clearable
                      counter
                      outlined
                      v-model="form.password"
                      type="password"
                      label="Ingresar la contraseña"
                      data-vv-scope="crear_password"
                      data-vv-name="contraseña"
                      v-validate="'required|min:6'"
                    ></v-text-field>
                    <FormError
                      :attribute_name="'crear_password.contraseña'"
                      :errors_form="errors"
                    ></FormError>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" @click="dialog_password = false">
                Cancelar
              </v-btn>
              <v-btn
                color="blue darken-1"
                @click="validar_formulario_password('crear_password')"
              >
                Guardar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import FormError from './components/shared/FormError'

export default {
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog_password: false,
      drawer: false,
      menu: false,
      form: {
        id: 0,
        password: null,
      },
      logo: 'conectividad_3.jpeg',
      tags: ['Negro', 'Naranja', 'Blanco'],
      style: 'color: white;',
      seleccionado: '',
    }
  },
  created() {},
  methods: {
    logout() {
      let self = this
      self.loading = true
      self.$store.state.services.loginService
        .logout()
        .then((r) => {
          self.$store.dispatch('logout')
          self.$router.push('/login')
          self.drawer = null
          self.$store.state.is_login = false
          self.loading = false
        })
        .catch((e) => {})
    },

    redirect(item) {
      this.$router.push({ path: item })
    },

    mostar() {
      let self = this
      self.drawer = self.drawer ? false : true
    },

    cambiar_password() {
      this.loading = true
      this.form.id = this.$store.state.usuario.id
      this.dialog_password = true
      this.loading = false
    },

    validar_formulario_password(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.$swal({
            title: 'Cambiar contraseña',
            text: '¿Está seguro de realizar esta acción?',
            type: 'warning',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.form.password = window.btoa(this.form.password)
              this.loading = true
              this.$store.state.services.userService
                .reset(this.form)
                .then((r) => {
                  if (r.response) {
                    if (r.response.data.code === 404) {
                      this.$toastr.warning(r.response.data.error, 'Advertencia')
                    } else if (r.response.data.code === 423) {
                      this.$toastr.warning(r.response.data.error, 'Advertencia')
                    } else {
                      for (let value of Object.values(r.response.data)) {
                        this.$toastr.error(value, 'Mensaje')
                      }
                    }

                    this.loading = false
                    return
                  }

                  this.$toastr.success(r.data, 'Mensaje')
                  this.form.id = 0
                  this.form.password = null
                  this.dialog_password = false
                  this.logout()
                })
                .catch((r) => {
                  this.loading = false
                })
            } else {
              this.close()
            }
          })
        }
      })
    },

    cambiar_logo(color) {
      const COLORES_EXISTENTES = {
        Negro: {
          logo: 'conectividad_3.jpeg',
          style: 'color: white;',
          theme: false,
        },
        Naranja: {
          logo: 'conectividad_1.jpeg',
          style: 'color: white;',
          theme: true,
        },
        Blanco: {
          logo: 'conectividad_2.jpeg',
          style: 'color: black;',
          theme: true,
        },
      }

      const existe = COLORES_EXISTENTES[color] || COLORES_EXISTENTES['Negro']
      this.seleccionado = color
      this.$cookies.set('color', color)
      this.$cookies.set('logo', existe.logo)
      this.$cookies.set('style', existe.style)
      this.$cookies.set('theme', existe.theme)
      this.logo = this.$cookies.get('logo')
      this.style = this.$cookies.get('style')
      this.$vuetify.theme.dark = this.$cookies.get('theme')
      this.$forceUpdate()
    },

    ver(item) {
      return _.includes(this.$store.state.permissions, item)
    },

    ir(ruta) {
      this.$router.push(`/${ruta}`)
    },
  },
  computed: {
    isLogin() {
      let self = this

      if (self.$store.state.is_login) {
        this.drawer = true
        this.logo = this.$cookies.get('logo')
        this.style = this.$cookies.get('style')
        this.seleccionado = this.$cookies.get('color')
          ? this.$cookies.get('color')
          : 'Negro'
        this.$vuetify.theme.dark = this.$cookies.get('theme')
      } else {
        this.$vuetify.theme.dark = false
      }

      return self.$store.state.is_login
    },

    usuario_nombre() {
      if (!_.isEmpty(this.$store.state.usuario)) {
        return this.$store.state.usuario.full_name
      }
      return ''
    },

    usuario_cui() {
      if (!_.isEmpty(this.$store.state.usuario)) {
        return this.$store.state.usuario.cui
      }
      return ''
    },

    usuario_email() {
      if (!_.isEmpty(this.$store.state.usuario)) {
        return this.$store.state.usuario.email
      }
      return ''
    },

    usuario_foto() {
      if (!_.isEmpty(this.$store.state.usuario)) {
        return this.$store.state.usuario.picture
      }
      return null
    },

    usuario_inicial() {
      if (!_.isEmpty(this.$store.state.usuario)) {
        return (
          this.$store.state.usuario.name.charAt(0).toUpperCase() +
          this.$store.state.usuario.surname.charAt(0).toUpperCase()
        )
      }
      return 'NA'
    },

    getMenu() {
      let self = this
      return self.$store.state.menu
    },

    getLogo() {
      let self = this
      return `${self.$store.state.base_url}img/${self.logo}`
    },
  },
}
</script>
