import Vue from "vue";
import VueRouter from "vue-router";
import ExampleComponent from "./components/ExampleComponent";

Vue.use(VueRouter);

export default new VueRouter({
    routes: [
        {
            path: "/",
            component: ExampleComponent
        }
    ]
    // mode: "history" //this turns off backwards compatibility nad removes # from our toute links, does not work on my browser
});
