import Axios from 'axios'
import auth from '../auth'
import {
  isNullOrUndefined
} from 'util';

/* ::::::::::::::::::::::::::::::::::::::::::::: SEGURIDAD :::::::::::::::::::::::::::::::::::::::::::::::: */
import loginService from '../services/seguridad/login/LoginService'
import menuService from '../services/seguridad/menu/MenuService'
import rolMenuService from '../services/seguridad/rol/RolMenuService'
import rolService from '../services/seguridad/rol/RolService'
import userRolService from '../services/seguridad/usuario/UserRolService'
import userService from '../services/seguridad/usuario/UserService'
/* ::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE SEGURIDAD ::::::::::::::::::::::::::::::::::::: */

/* ::::::::::::::::::::::::::::::::::::::::::::: CATALOGO :::::::::::::::::::::::::::::::::::::::::::::::: */
import departamentService from '../services/catalogo/departamento/DepartamentService'
import municipalityService from '../services/catalogo/municipio/MunicipalityService'
/* :::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE CATALOGO ::::::::::::::::::::::::::::::::::::: */

/* ::::::::::::::::::::::::::::::::::::::::::::: PRINCIPAL :::::::::::::::::::::::::::::::::::::::::::::::: */
import ventaService from '../services/principal/VentaService'
import bitacoraService from '../services/principal/BitacoraService'
/* :::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE PRINCIPAL ::::::::::::::::::::::::::::::::::::: */


// Para desarrollo
let baseUrl = 'http://172.31.253.59/service_detalle/'
//let baseUrl = 'http://localhost:8001/apis/public/' //base url desarrollo
let token_data = $cookies.get('token_data')

// Axios Configuration
Axios.defaults.headers.common.Accept = 'application/json'
if (token_data !== null) {
  Axios.defaults.headers.common.Authorization = `Bearer ${token_data.access_token}`
}

const instance = Axios.create();
Axios.interceptors.response.use(response => {
  return response
}, error => {
  if (error.response.status === 401) {
    var token_data = $cookies.get('token_data')
    if (isNullOrUndefined(token_data)) {
      return error
    }

    var original_request = error.config
    return refreshToken().then(res => {
      auth.saveToken(res.data)
      original_request.headers['Authorization'] = 'Bearer ' + res.data.access_token
      return Axios.request(original_request)
    })
  }

  return error
});

function refreshToken() {
  var data = auth.getRefreshToken()
  return new Promise(function (resolve, reject) {
    instance.post(baseUrl + 'service/passport/generar/token', data)
      .then(r => {
        resolve(r)
      }).catch(e => {
        reject(r)
      })
  })
}

instance.interceptors.response.use(response => {
  return response
}, error => {
  if (error.response.status === 401) {
    auth.noAcceso()
  }

  return error
});

export default {
//Seguridad
  loginService: new loginService(Axios, baseUrl),
  menuService: new menuService(Axios, baseUrl),
  rolMenuService: new rolMenuService(Axios, baseUrl),
  rolService: new rolService(Axios, baseUrl),
  userRolService: new userRolService(Axios, baseUrl),
  userService: new userService(Axios, baseUrl),
//Catalogo
  departamentService: new departamentService(Axios, baseUrl),
  municipalityService: new municipalityService(Axios, baseUrl),
//Principal
  ventaService: new ventaService(Axios, baseUrl),
  bitacoraService: new bitacoraService(Axios, baseUrl)
}
