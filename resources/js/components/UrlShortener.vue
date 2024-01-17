<template>
    <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row shortner">
                       <div class="section-heading text-center">
                            <h1>URL Shortener</h1>                        
                            <div>
                                <form class="form">
                                    <div class="input-group mb-3">
                                        <input 
                                            type="text" 
                                            id="p1" 
                                            v-model="url" 
                                            class="form-control addUrlInput"
                                            placeholder="Add Url Here" 
                                            aria-label="Add Url Here"                                            
                                            aria-describedby="button-addon2"
                                        />
                                    </div>
                                    <div>
                                        <button v-on:click.prevent="shortenUrl" class="btn btn-dark" id="button-addon2">
                                            Shorten
                                        </button>
                                    </div>
                                </form>
                                <br>
                                <div class="copyLink mb-5">
                                    <span v-if="!!response">Shortened URL: </span>
                                    <a id="output_url" v-bind:href="hash" target="_blank"></a>
                                </div>
                                <div>
                                    <p>Google Safe Browsing Check</p>
                                    <span id="check_url"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data(){
            return {
                url: '',
                urlNotFound: true,
                response: null,
                hash: null,
            }
        },
        methods: {
            shortenUrl() {
                let self = this;
                let oldUrl = self.url;
                self.safeBrowsingCheck(oldUrl);
                axios.post('/url/shorten', {
                    url: oldUrl,
                }).then(function (response) {
                    console.log("res", response)
                    self.response  = response.data;
                    self.hash = response.data.split("/").slice(-1);
                    $("#output_url").html(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            redirectToURL() {
                axios.get(`/${this.response}`)
            },

            async safeBrowsingCheck(_url) {
                try {
                    const apiKey = 'AIzaSyD3uFWKmWWDoGavt2BIhEOrVU0PG4GoPfs';
                    const apiUrl = `https://safebrowsing.googleapis.com/v4/threatMatches:find?key=${apiKey}`;

                    const requestData = {
                    client: {
                        clientId: '691935475394-e8f4gka99b9cnitse8h0eqs8kkhbgcn5.apps.googleusercontent.com',
                        clientVersion: '1.0.0',
                    },
                    threatInfo: {
                        threatTypes: ['MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION'],
                        platformTypes: ['ANY_PLATFORM'],
                        threatEntryTypes: ['URL'],
                        threatEntries: [{ url: _url }],
                    },
                    };

                    const response = await axios.post(apiUrl, requestData);
                    if (response.data.matches && response.data.matches.length > 0) {
                        this.result = 'This URL is unsafe!';
                        $("#check_url").html(this.result);
                    } else {
                        this.result = 'This URL is safe.';
                        $("#check_url").html(this.result);
                    }
                } catch (error) {
                    console.error('Error during Safe Browsing lookup:', error);
                }
            },
        }
    }
</script>
<style scoped>
</style>