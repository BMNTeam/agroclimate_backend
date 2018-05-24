import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';


import { AppComponent } from './app.component';
import { HeaderComponent } from './header/header.component';
import { MainComponent } from './main/main.component';
import { FooterComponent } from './footer/footer.component';
import { SelectDataComponent } from './database/select-data/select-data.component';
import {HttpClientModule} from "@angular/common/http";
import {FormsModule} from "@angular/forms";
import { ShowDataComponent } from './database/show-data/show-data.component';
import {RouterModule, Routes} from "@angular/router";
import { EditDataComponent } from './database/edit-data/edit-data.component';
import { SettingsComponent } from './settings/settings/settings.component';


const appRoutes: Routes = [
    {path: 'show/:meteostationId/:yearStart', component: ShowDataComponent},
    {path: 'show/:meteostationId/:yearStart/:yearEnd', component: ShowDataComponent},
    {path: '',component: SelectDataComponent    },
    {path: ':meteostationId/:yearStart',component: SelectDataComponent    },
    {path: 'edit/:meteostationId/:yearStart', component: EditDataComponent},
    {path: 'settings', component: SettingsComponent}
]

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    FooterComponent,
      MainComponent,
    SelectDataComponent,
    ShowDataComponent,
    EditDataComponent,
    SettingsComponent
  ],
  imports: [
    BrowserModule,
      HttpClientModule,
      FormsModule,
      RouterModule.forRoot(
          appRoutes
      )
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
