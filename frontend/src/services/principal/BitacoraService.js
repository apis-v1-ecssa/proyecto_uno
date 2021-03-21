class BitacoraService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = `${baseUrl}service/rest/v1/principal/bitacora`
    }

    index() {
        let self = this;
        return self.axios.get(`${self.baseUrl}`);
    }

    venta(data) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/venta/${data.id}`);
    }
}

export default BitacoraService