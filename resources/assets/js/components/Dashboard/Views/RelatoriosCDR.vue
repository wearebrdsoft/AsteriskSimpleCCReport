<template>
    <div class="row">
      <div class="col-lg-12 col-md-12">
          <div class="card">
            <div class="header">
              <h4 class="title">Filtro</h4>
            </div>
            <div class="content">
              <form action="/api/cdr" target="_blank" method="post" >
                <div class="row">
                    <div class="col-md-5">
                      <div class="form-group">
                          <label>Data inicial</label>
                          <datepicker
                          input-class="form-control border-input"
                          language="pt-br"
                          v-model="filtros.di"
                          name="di"
                          disabled="false"
                          require=true
                          format="dd/MM/yyyy" />
                      </div>
                    </div>
                    <div class="col-md-5">
                      <div class="form-group">
                          <label>Data inicial</label>
                          <datepicker
                          input-class="form-control border-input"
                          language="pt-br"
                          v-model="filtros.df"
                          require=true
                          name="df"
                          format="dd/MM/yyyy"/>
                      </div>
                      <input type="hidden" name="xls" value="true">
                      <input type="hidden" name="r_tp" value="cdr">
                      <input type="hidden" name="title_rt" value="cdr">
                      <input type="hidden" name="_token" v-model='token' value="">
                      <input type="hidden" name="title_rt" v-model='filtros.title_rt' value="">
                    </div>
                    <div style="margin-top: 28px;" class="col-md-2">
                      <button type="submit"
                      class="btn btn-info btn-fill btn-wd" @click.prevent="getReport">
                        Buscar
                      </button>
                    </div>
                </div>
                <div v-if="this.filtros.r_tp=='exs'" class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <select v-model="filtros.tp_extrato" class="form-control border-input" name="tp_extrato">
                                <option value="">Todos</option>
                                <option value="abandonada">Chamada Abandonada</option>
                                <option value="completada">Chamada Completada</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
              </form>
            </div>
          </div>
      </div>
      <div v-if="showreport" style="z-index: 0;" class="col-lg-12 col-md-12">
          <div class="card">
            <div class="header">
              <h4 style="float: left;" class="title">Registro de Chamadas</h4>&nbsp;
              <button @click="exportXLS" type="button" class="btn btn-default btn-sm">
                <span class="glyphicon glyphicon-save"></span>
                Exportar para Excel
              </button>
            </div>
            <div v-html="dados"  class="content">
            </div>
        </div>
      </div>
    </div>
</template>
<script>
import Datepicker from 'vuejs-datepicker';
export default {
    components: { Datepicker },
    methods: {
        getReport:function(){
            this.dados = "<h3>Carregando Relatório...</h3>";
            this.showreport = true;
            axios.post('api/cdr',this.filtros).
            then(r => {
                this.dados = r.data;
            }).
            catch(e =>{ alert(e) })
        },
        tpRelatorio:function(e){
            this.title= e.target.options[e.target.options.selectedIndex].innerHTML;
            this.filtros.title_rt = this.title;
            this.showreport = false;
        },
        exportXLS:function(){
            $('form').submit();
        }
    },
    data(){
        return {
            token: document.head.querySelector('meta[name="csrf-token"]').content,
            relatorio_rt:'',
            title:'',
            dados:'',
            filtros:{
                di:'',df:'',r_tp:'',tp_extrato:'',title_rt:'',xls:false
            },
            showreport:false,
        }
    }
}

</script>
<style>

</style>
