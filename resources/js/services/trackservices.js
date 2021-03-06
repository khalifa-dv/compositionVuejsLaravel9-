import axios from "axios";
import { ref, watch } from "vue";
import router from '../router/index.js'


export default function useTracks() {
    const tracks = ref([]);
    const errors = ref('');
    const track = ref([]);
    let imageFile = ref("");
    let imageUrl = ref("");

    const getTrack = async (id) => {

        console.log(id);

        let response = await axios.get('/api/Tracks/' + id);
        track.value = response.data.data;
        console.log(response.data.data.image);

    };

    const getTracks = async (page = 1) => {
        let response = await axios.get('/api/Tracks?page=' + page);
        tracks.value = response.data;

    };
    const createTracks = async (data) => {
        errors.value = ''
        try {
            await axios.post('/api/Tracks', data);
            await router.push({ name: 'tracks.index' })
        } catch (e) {
            if (e.response.status === 422) {

                errors.value = e.response.data.errors;
            };
        };
    };
    const editTrack = async (data) => {
        errors.value = ''
        try {
            await axios.post('/api/Tracks', data);
            await router.push({ name: 'tracks.index' })
        } catch (e) {
            if (e.response.status === 422) {

                errors.value = e.response.data.errors;
            };
        };
    };
    const UpdateTracks = async (id) => {
        errors.value = ''
        try {
            await axios.put('/api/Tracks/'+ id,track.value);
            await router.push({ name: 'tracks.index' })
        } catch (e) {
            if (e.response.status === 422) {

                errors.value = e.response.data.errors;
                console.log(e.response.data.errors);
            };
        };
    };
    const destroyTrack= async(id)=>{

        if(! window.confirm('Are u sure !'))return;
        await axios.delete('/api/Tracks/'+ id);
        await getTracks();
    }

    function handleImageSelected(event) {
                let file = event.target.files[0];
                console.log(file);
                let reader = new FileReader();

                errors.value ="";
                if (file=== 0) {
                    imageFile.value = "";
                    imageUrl.value = "";
                    return;}
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    console.log('RESULT', reader.result)
                     this.form.image = reader.result;
                     track.value.image=reader.result;


                    }
                     reader.readAsDataURL(file);
                     imageFile.value =file;
                }else{
                    // alert('File size can not be bigger than 2 MB')
                    errors.value ={image:['File size can not be bigger than 2 MB']};

                }


        }





    watch(imageFile, (imageFile) => {
        if (!(imageFile instanceof File)) {
            return;
        }

        let fileReader = new FileReader();

        fileReader.readAsDataURL(imageFile);

        fileReader.addEventListener("load", () => {
            imageUrl.value = fileReader.result;
        });
    });
        function uploadAudio(event){
            let file = event.target.files[0];
            console.log(file);
            let reader = new FileReader();

            errors.value ="";

            if(file['size'] < 8011775)
            {
                reader.onloadend = (file) => {
                console.log('RESULT', reader.result)
                 this.form.audio = reader.result;
                 track.value.audio=reader.result;


                }
                 reader.readAsDataURL(file);

            }else{
                // alert('File size can not be bigger than 2 MB')
                errors.value ={audio:['File size can not be bigger than 8 MB']};

            }


        }

    return {
        tracks,
        getTracks,
        createTracks,
        imageFile,
        imageUrl,
        handleImageSelected,
        errors,
        track,
        editTrack,
        getTrack,
        UpdateTracks,
        destroyTrack,
        uploadAudio

    }
}
