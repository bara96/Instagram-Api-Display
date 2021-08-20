# Instagram-Api-Display
Basic example class to interact with Instagram Graph API

https://developers.facebook.com/docs/instagram-basic-display-api

## Setup Instagram API
https://developers.facebook.com/docs/instagram-basic-display-api/getting-started#get-started

### Create an Application
First of all, create an Application on [facebook developers](https://developers.facebook.com/apps).  
I've choose "<i>None</i>" on my app type.


### Basic Settings
- Go into <i>Products</i> section and add <code>Instagram Graph API</code> to your application.  
- Enter <i>Basic Display</i> section from menu and set the <code>Client OAuth Settings</code> with a valid url of your App.
- Enter <i>Roles</i> section from menu and <code>Add Instagram Testers</code>.

## Setup Web Application
- Files such <code>index.php</code>,<code>auth.php</code>,<code>logout.php</code> are test files only.  
- Setup your own files with your <code>clientSecret</code> and <code>clientId</code>.  
- Remembere also to set the same <code>redirect url</code> you've set into the <i>Client OAuth Settings</i>, for example: mine points to <code> https://server_base_url/auth.php </code>.

## API Usage
1. <code>getInstagramLoginUrl()</code> will redirect the user into the Instagram login form.
2. <code>performInstagramAuthentication()</code> will perform a login and will try to obtain a long-lived access token, in order to use Instagram API.
3. <code>getUserProfile()</code>, <code>getUserMedia()</code>, <code>getMedia()</code> are three examples of Instagram API endpoint usages.