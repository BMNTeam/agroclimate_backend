import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {SelectDataComponent} from "../database/select-data/select-data.component";
import {ConnectionService} from "../database/connection.service";
import {ShowDataComponent} from "../database/show-data/show-data.component";
import {EditDataComponent} from "../database/edit-data/edit-data.component";

@NgModule({
  imports: [
    CommonModule
  ],
    providers: [ConnectionService],
  declarations: [ SelectDataComponent, ShowDataComponent, EditDataComponent]
})
export class MainModule { }
