import Vue from 'vue'
import Router from 'vue-router'
import store from '../store/index'
import multiguard from 'vue-router-multiguard' //Middelware
import goTo from 'vuetify/es5/services/goto'

import Default from '@/components/Default'
import Login from '@/components/login/Index'

//Seguridad
import Rol from '@/components/seguridad/RolComponent'
import Usuario from '@/components/seguridad/UsuarioComponent'

//Catalogo General
import Municipality from '@/components/catalogo/MunicipalityComponent'
import Departament from '@/components/catalogo/DepartamentComponent'

//Principal

Vue.use(Router)

//validar authenticacion
const isLoggedIn = (to, from, next) => {
  var user = store.state.usuario
  if (!_.isEmpty(user)) { }
  return store.state.is_login ? next() : next('/login')
}

const isLoggedOut = (to, from, next) => {
  return store.state.is_login ? next('/') : next()
}

const permissionsValidations = (to, from, next) => {
  var permissions = store.state.permissions
  var ruta = to.path.split('/').join('')
  var perm = _.includes(permissions, ruta)
  return perm ? next() : next('/')
}

const routes = [{
  path: '/',
  name: 'Default',
  component: Default,
  beforeEnter: multiguard([isLoggedIn])
},
{
  path: '/login',
  name: 'Login',
  component: Login,
  beforeEnter: multiguard([isLoggedOut])
},
//Seguridad
{
  path: '/rol',
  name: 'Rol',
  component: Rol,
  beforeEnter: multiguard([isLoggedIn, permissionsValidations])
}, {
  path: '/user',
  name: 'Usuario',
  component: Usuario,
  beforeEnter: multiguard([isLoggedIn, permissionsValidations])
},
//Catalogo General
{
  path: '/municipality',
  name: 'Municipality',
  component: Municipality,
  beforeEnter: multiguard([isLoggedIn, permissionsValidations])
}, {
  path: '/departament',
  name: 'Departament',
  component: Departament,
  beforeEnter: multiguard([isLoggedIn, permissionsValidations])
}
//Principal
]

export default new Router({
  scrollBehavior: (to, from, savedPosition) => {
    let scrollTo = 0

    if (to.hash) {
      scrollTo = to.hash
    } else if (savedPosition) {
      scrollTo = savedPosition.y
    }

    return goTo(scrollTo)
  },
  routes
})
