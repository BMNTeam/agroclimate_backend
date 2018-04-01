import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {SelectDataComponent} from "../database/select-data/select-data.component";
import {ConnectionService} from "../database/connection.service";
import {ShowDataComponent} from "../database/show-data/show-data.component";

@NgModule({
  imports: [
    CommonModule
  ],
    providers: [ConnectionService],
  declarations: [ SelectDataComponent, ShowDataComponent]
})
export class MainModule { }
