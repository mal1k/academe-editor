<template>
    <div class="search-filter">
        <div class="search-form">
        <div class="search-btn">
            <img src="/wp-content/themes/academe/assets/img/search.svg">
        </div>
        <input class="search-input" type="text" v-model="searchMovie" placeholder="Search title..">
        </div>
        <div  v-if="movies.length" class="search-form-results">
            <div>My List ({{searchFilter.length}} Movies)</div>
            <MovieItem 
            v-for="movie in searchFilter"
            :key="movie.kalturaID"
            :movie_data="movie"
            />
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import MovieItem from "../Shared/MovieItem"

    export default {
        name: 'search-filter',
        components: {
            MovieItem,
        },
        props: {},
        data() {
            return {
                searchMovie: '',
                movies: []
            }
        },

         computed: {
             store() {
                return this.$store.state.LessonEditor;
            },
            searchFilter(){
                if(this.searchMovie){
                    return this.movies.filter((item)=>{
                        return this.searchMovie.toLowerCase().split(' ').every(v => item.title.toLowerCase().includes(v))
                    })
                }else{
                    return this.movies;
                }
            }
        },
        methods: { },
        mounted() {
             axios.get('/academe/v1/my-movies')
            .then(response => {
                console.log(response)
                this.movies = response.data
            });
        },
    }
</script>

<style scoped>
.search-form {
 width: 100%;
    height: 42px;
    margin-bottom: 30px;
    border: 1px solid #C4C4C4;
    display: flex;
    align-items: center;
        padding: 10px;
}
.search-input {
    border: none;
    background: rgba(0,0,0,0);
    color: #FFF;
    height: 20px;
    flex: 1;
}
.search-input::placeholder {
    color: #FFF;
    opacity: .7;
    font-weight: 500;
}
.search-btn {
    margin-right: 10px;
}
.search-btn img {
    max-height: 30px;
}
</style>