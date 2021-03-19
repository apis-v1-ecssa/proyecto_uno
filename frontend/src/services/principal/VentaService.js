class VentaService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = `${baseUrl}service/rest/v1/principal`
    }

    aceptado() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/aceptado`);
    }

    facturado() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/facturado`);
    }

    completo() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/completo`);
    }

    anulado() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/anulado`);
    }

    completar_todo(data) {
        let self = this;
        return self.axios.put(`${self.baseUrl}/completar_todo/${data.id}`, data);
    }

    cancelar(data) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/cancelar/${data.id}`);
    }

    entregar(data) {
        let self = this;
        return self.axios.put(`${self.baseUrl}/entregar/${data.id}`, data);
    }

    aceptar(data) {
        let self = this;
        return self.axios.put(`${self.baseUrl}/aceptar/${data.id}`, data);
    }
}

export default VentaService