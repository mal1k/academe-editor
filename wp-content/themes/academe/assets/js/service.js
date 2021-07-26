import axios from 'axios';

axios.defaults.baseURL = window.wpApiSettings.root;
axios.defaults.headers.common['X-WP-Nonce'] = window.wpApiSettings.nonce;

export default {
    async getMyMovies() {
        try {
            const response = await axios.get("/academe/v1/my-movies")
            return response.data;
        } catch (err) {
            console.error('Failed to fetch my movies', err)
        }
    },
    async getMovieMeta(id) {
        try {
            const response = await axios.get(`/academe/v1/movies/${id}`)
            return response.data;
        } catch (err) {
            console.error(`Failed to info for movie with id ${id}`, err)
        }
    },
    async getMovieMetaByKalturaId(id) {
        try {
            const response = await axios.get(`/academe/v1/get-movie-meta-by-kaltura?id=${id}`)
            return response.data;
        } catch (err) {
            console.error(`Failed to info for movie with id ${id}`, err)
        }
    },
    async getKalturaConfig() {
        try {
            const response = await axios.get("/academe/v1/kaltura-config");
            return response.data;
        } catch (err) {
            console.error(`Failed to fetch kaltura config`, err)
        }
    },
}