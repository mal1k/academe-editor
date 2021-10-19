import store from './vuex-store';

export default {

    timeStringToSeconds(timeString) {
        let seconds = 0;
        if (timeString !== null && timeString !== "") {
            const [hh, mm, ss] = timeString.split(":");
            seconds = +hh * 60 * 60 + +mm * 60 + +ss;
        }
        return seconds;
    },
    secondsToTimeString(input) {
        const sec_num = parseInt(input, 10);
        let hours = Math.floor(sec_num / 3600);
        let minutes = Math.floor((sec_num - hours * 3600) / 60);
        let seconds = sec_num - hours * 3600 - minutes * 60;

        if (hours < 10) {
            hours = "0" + hours;
        }
        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        if (seconds < 10) {
            seconds = "0" + seconds;
        }
        return hours + ":" + minutes + ":" + seconds;
    },
    secondsToHumanReadable(seconds) {
        let timestring = this.secondsToTimeString(seconds);
        let timestring_arr = timestring.split(':');
        let output;
        if (parseInt(timestring_arr[0]) === 0) {
            output = parseInt(timestring_arr[1])+'m '+ parseInt(timestring_arr[2])+'s';
        } else {
            output = parseInt(timestring_arr[0])+'h '+ parseInt(timestring_arr[1])+'m';
        }
        return output;
    },
    participantMovies() {
        let storage = store.state.LessonEditor;
        let movies = [];
        if (storage.slides.length) {
            storage.slides.forEach((slide) => {
                if (slide.slide_type === 'movie') {
                    if (slide.movie_meta) {
                        movies.push(slide.movie_meta.title);
                    }
                }
            })
        }
        return movies.join(', ');
    }
};
