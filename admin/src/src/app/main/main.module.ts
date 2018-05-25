import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {ConnectionService} from "../database/connection.service";

@NgModule({
  imports: [
    CommonModule
  ],
    providers: [ConnectionService],
})
export class MainModule { }
