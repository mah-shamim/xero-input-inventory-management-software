/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import {Request} from "./Request";
import {Report} from './Report';
import {SalePurchaseMethods} from './SalePurchseMethods';
export class Erp{
    constructor() {
        this.request = new Request()
        this.report = new Report()
        this.salePurchase=new SalePurchaseMethods()
    }
}