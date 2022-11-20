import './bootstrap';
import { AuthManager } from './managers/authManager';
import { Router } from './services/router.js';


let router = new Router

router.addRoute("/login", AuthManager)

router.run()