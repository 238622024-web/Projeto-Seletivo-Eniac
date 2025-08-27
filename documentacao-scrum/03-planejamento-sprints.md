# 🗓️ Planejamento de Sprints - Projeto ENIAC LINK+

## 📅 Cronograma Geral do Projeto

**Duração Total:** 3 meses (12 semanas)
**Número de Sprints:** 6 sprints de 2 semanas cada
**Início:** Sprint 0 - Preparação e Setup
**Velocidade Estimada:** 16-20 story points por sprint (baseado no time de 3-4 pessoas)

---

## 🎯 Roadmap de Releases

### 🏁 Release 1 - MVP Básico (Final Sprint 2)
**Objetivo:** Sistema funcional básico para candidatos e RH
**Funcionalidades Principais:**
- Cadastro e login de candidatos
- Listagem e busca de vagas
- Candidatura básica
- Painel administrativo simples

### 🚀 Release 2 - Funcionalidades Avançadas (Final Sprint 4)
**Objetivo:** Sistema completo com gestão avançada
**Funcionalidades Principais:**
- Gestão completa de candidaturas
- Agendamento de entrevistas
- Dashboard analítico
- Sistema de comunicação

### 🎨 Release 3 - Otimizações e Melhorias (Final Sprint 6)
**Objetivo:** Sistema otimizado e pronto para produção
**Funcionalidades Principais:**
- Otimizações de performance
- Recursos avançados de administração
- Melhorias de UX/UI
- Integrações externas

---

## 🏃‍♂️ Sprint 0 - Preparação e Setup
**Duração:** 1 semana
**Objetivo:** Preparar ambiente e alinhar equipe

### 🎯 Objetivos Principais
- [ ] Setup do ambiente de desenvolvimento
- [ ] Definição da arquitetura técnica
- [ ] Configuração de ferramentas de desenvolvimento
- [ ] Alinhamento da equipe sobre processos

### 📋 Backlog da Sprint 0
| Atividade | Responsável | Estimativa | Status |
|-----------|-------------|------------|--------|
| Setup repositório Git | Dev Team | 1d | ⏳ |
| Configuração banco de dados | Backend Dev | 1d | ⏳ |
| Setup ambiente local | Dev Team | 1d | ⏳ |
| Definição de padrões de código | Dev Team | 0.5d | ⏳ |
| Configuração de CI/CD básico | Backend Dev | 1d | ⏳ |
| Workshop Scrum para equipe | Scrum Master | 0.5d | ⏳ |

### 🎯 Definition of Ready
- [ ] Ambiente de desenvolvimento configurado
- [ ] Banco de dados estruturado e populado
- [ ] Padrões de desenvolvimento definidos
- [ ] Equipe alinhada com processo Scrum

---

## 🚀 Sprint 1 - Fundação do Sistema
**Duração:** 2 semanas (Semanas 2-3)
**Objetivo:** Criar base sólida do sistema com funcionalidades essenciais

### 🎯 Meta da Sprint
*"Ao final desta Sprint, candidatos poderão se cadastrar na plataforma e visualizar vagas disponíveis, enquanto administradores poderão fazer login e acessar painel básico."*

### 📊 Capacity Planning
- **Total da equipe:** 40 horas/pessoa (4 pessoas × 10 dias úteis)
- **Velocity esperada:** 18 story points
- **Buffer para impedimentos:** 10%

### 📋 Sprint Backlog

| User Story | Story Points | Assignee | Status |
|------------|-------------|----------|--------|
| US01 - Cadastro de Candidato | 8 | Backend + Frontend | 📋 |
| US02 - Busca de Vagas (básica) | 5 | Frontend + Backend | 📋 |
| US11 - Gestão de Usuários Admin (básica) | 5 | Backend | 📋 |

**Total:** 18 story points

### 🔧 Tasks Técnicas
- [ ] Implementar estrutura MVC básica
- [ ] Criar sistema de autenticação
- [ ] Desenvolver formulário de cadastro responsivo
- [ ] Implementar validações de dados
- [ ] Criar página de listagem de vagas
- [ ] Desenvolver painel administrativo básico
- [ ] Implementar upload de currículo
- [ ] Testes unitários básicos

### 🎯 Critérios de Aceitação da Sprint
- [ ] Candidatos podem se cadastrar com sucesso
- [ ] Upload de currículo funcionando
- [ ] Listagem de vagas exibindo corretamente
- [ ] Admins podem fazer login e acessar painel
- [ ] Sistema responsivo em mobile
- [ ] Validações de dados funcionando

---

## 🎪 Sprint 2 - MVP Completo
**Duração:** 2 semanas (Semanas 4-5)
**Objetivo:** Completar MVP com candidatura e gestão básica de RH

### 🎯 Meta da Sprint
*"Ao final desta Sprint, teremos um sistema funcional onde candidatos podem se candidatar a vagas e RH pode gerenciar candidaturas básicas - nosso MVP estará pronto!"*

### 📊 Capacity Planning
- **Velocity esperada:** 20 story points (aumento baseado em learning)
- **Foco:** Finalizar fluxo completo candidato-vaga-RH

### 📋 Sprint Backlog

| User Story | Story Points | Assignee | Status |
|------------|-------------|----------|--------|
| US03 - Candidatura a Vaga | 5 | Full Team | 📋 |
| US06 - Cadastro de Vagas | 5 | Backend + Frontend | 📋 |
| US07 - Gestão de Candidaturas (básica) | 8 | Backend + Frontend | 📋 |
| Melhorias UX/UI Sprint 1 | 2 | Frontend | 📋 |

**Total:** 20 story points

### 🔧 Tasks Técnicas
- [ ] Implementar sistema de candidaturas
- [ ] Criar formulário de cadastro de vagas
- [ ] Desenvolver listagem de candidatos por vaga
- [ ] Implementar mudança de status de candidatura
- [ ] Criar notificações por email básicas
- [ ] Melhorar interface baseada em feedback
- [ ] Implementar sistema de filtros
- [ ] Testes de integração

### 🏁 Release 1 - MVP
**Entregáveis:**
- [ ] Sistema completo de cadastro candidatos
- [ ] Sistema completo de gestão de vagas
- [ ] Fluxo de candidatura funcionando
- [ ] Painel RH com gestão básica
- [ ] Deploy em ambiente de homologação

---

## 🔍 Sprint 3 - Refinamento e Experiência do Usuário
**Duração:** 2 semanas (Semanas 6-7)
**Objetivo:** Melhorar experiência do usuário e adicionar funcionalidades complementares

### 🎯 Meta da Sprint
*"Ao final desta Sprint, usuários terão uma experiência mais rica com atualização de perfil, acompanhamento de status e interface mais polida."*

### 📊 Capacity Planning
- **Velocity esperada:** 19 story points
- **Foco:** UX e funcionalidades de valor agregado

### 📋 Sprint Backlog

| User Story | Story Points | Assignee | Status |
|------------|-------------|----------|--------|
| US04 - Atualização de Perfil | 3 | Frontend + Backend | 📋 |
| US05 - Acompanhamento de Status | 8 | Full Team | 📋 |
| US02 - Busca de Vagas (filtros avançados) | 3 | Frontend | 📋 |
| Melhorias de Performance | 5 | Backend | 📋 |

**Total:** 19 story points

### 🔧 Tasks Técnicas
- [ ] Implementar edição de perfil do candidato
- [ ] Criar dashboard de status para candidatos
- [ ] Desenvolver sistema de notificações
- [ ] Implementar filtros avançados de busca
- [ ] Otimizar consultas do banco de dados
- [ ] Melhorar responsividade mobile
- [ ] Implementar cache básico
- [ ] Testes de usabilidade

### 🎯 Critérios de Aceitação da Sprint
- [ ] Candidatos podem editar perfil completamente
- [ ] Dashboard de status funcionando
- [ ] Filtros de busca operacionais
- [ ] Notificações por email funcionando
- [ ] Performance melhorada em 30%
- [ ] Interface 100% responsiva

---

## 📊 Sprint 4 - Funcionalidades Avançadas de RH
**Duração:** 2 semanas (Semanas 8-9)
**Objetivo:** Implementar funcionalidades avançadas para gestão de RH

### 🎯 Meta da Sprint
*"Ao final desta Sprint, profissionais de RH terão ferramentas completas para gerenciar processos seletivos, incluindo entrevistas e comunicação com candidatos."*

### 📊 Capacity Planning
- **Velocity esperada:** 21 story points
- **Foco:** Ferramentas avançadas de RH

### 📋 Sprint Backlog

| User Story | Story Points | Assignee | Status |
|------------|-------------|----------|--------|
| US08 - Agendamento de Entrevistas | 13 | Full Team | 📋 |
| US10 - Comunicação com Candidatos | 8 | Backend + Frontend | 📋 |

**Total:** 21 story points

### 🔧 Tasks Técnicas
- [ ] Implementar sistema de calendário
- [ ] Criar agendamento de entrevistas
- [ ] Desenvolver sistema de mensagens
- [ ] Implementar templates de email
- [ ] Criar notificações automáticas
- [ ] Desenvolver histórico de comunicações
- [ ] Integração com Google Calendar (opcional)
- [ ] Testes de fluxo completo

### 🏁 Release 2 - Sistema Completo
**Entregáveis:**
- [ ] Sistema completo de entrevistas
- [ ] Comunicação interna funcionando
- [ ] Todos os fluxos de RH operacionais
- [ ] Deploy em ambiente de produção

---

## 📈 Sprint 5 - Analytics e Administração
**Duração:** 2 semanas (Semanas 10-11)
**Objetivo:** Implementar analytics, relatórios e funcionalidades administrativas avançadas

### 🎯 Meta da Sprint
*"Ao final desta Sprint, gestores terão dashboards completos com métricas e relatórios, além de ferramentas administrativas robustas."*

### 📊 Capacity Planning
- **Velocity esperada:** 21 story points
- **Foco:** Analytics e administração

### 📋 Sprint Backlog

| User Story | Story Points | Assignee | Status |
|------------|-------------|----------|--------|
| US09 - Dashboard Analítico | 8 | Backend + Frontend | 📋 |
| US12 - Backup e Segurança | 13 | Backend | 📋 |

**Total:** 21 story points

### 🔧 Tasks Técnicas
- [ ] Implementar métricas e KPIs
- [ ] Criar gráficos e relatórios
- [ ] Desenvolver sistema de backup
- [ ] Implementar logs de auditoria
- [ ] Criar exportação de dados
- [ ] Implementar política de senhas
- [ ] Desenvolver monitoramento de sistema
- [ ] Testes de segurança

### 🎯 Critérios de Aceitação da Sprint
- [ ] Dashboard com métricas funcionando
- [ ] Relatórios exportáveis implementados
- [ ] Sistema de backup automático
- [ ] Logs de auditoria completos
- [ ] Política de segurança implementada

---

## 🏆 Sprint 6 - Finalização e Otimizações
**Duração:** 2 semanas (Semanas 12-13)
**Objetivo:** Finalizar projeto com otimizações, configurações avançadas e preparação para produção

### 🎯 Meta da Sprint
*"Ao final desta Sprint, teremos um sistema robusto, otimizado e completamente pronto para uso em produção, com todas as configurações e integrações necessárias."*

### 📊 Capacity Planning
- **Velocity esperada:** 18 story points
- **Foco:** Finalização e polish

### 📋 Sprint Backlog

| User Story | Story Points | Assignee | Status |
|------------|-------------|----------|--------|
| US13 - Configurações do Sistema | 8 | Backend | 📋 |
| Otimizações Finais | 5 | Full Team | 📋 |
| Documentação e Treinamento | 5 | Full Team | 📋 |

**Total:** 18 story points

### 🔧 Tasks Técnicas
- [ ] Implementar configurações avançadas
- [ ] Otimizar performance geral
- [ ] Finalizar documentação técnica
- [ ] Criar manual do usuário
- [ ] Implementar monitoramento avançado
- [ ] Configurar ambiente de produção
- [ ] Testes de carga e stress
- [ ] Treinamento da equipe de suporte

### 🏁 Release 3 - Sistema Final
**Entregáveis:**
- [ ] Sistema completamente funcional
- [ ] Documentação completa
- [ ] Ambiente de produção configurado
- [ ] Equipe treinada
- [ ] Monitoramento implementado

---

## 📊 Métricas e Acompanhamento

### 🎯 Velocity Tracking
| Sprint | Story Points Planejados | Story Points Entregues | Velocity |
|--------|------------------------|----------------------|----------|
| Sprint 1 | 18 | - | - |
| Sprint 2 | 20 | - | - |
| Sprint 3 | 19 | - | - |
| Sprint 4 | 21 | - | - |
| Sprint 5 | 21 | - | - |
| Sprint 6 | 18 | - | - |
| **Total** | **117** | **-** | **-** |

### 📈 Burndown do Projeto
- **Total de Story Points:** 117
- **Sprints Planejadas:** 6
- **Velocity Média Esperada:** 19.5 points/sprint

### 🎯 KPIs do Projeto
- **Scope Creep:** < 10% de mudanças no backlog
- **Velocity Variation:** < 20% entre sprints
- **Bug Rate:** < 5% de bugs em produção
- **User Satisfaction:** > 4.0/5.0 nos testes com usuários
- **Performance:** Tempo de resposta < 2s para 95% das requisições

---

## 🔄 Cerimônias Scrum

### 📅 Calendário de Cerimônias

#### Sprint Planning
- **Frequência:** Início de cada Sprint (segunda-feira)
- **Duração:** 4 horas máximo
- **Participantes:** Todo o time Scrum
- **Objetivo:** Planejar trabalho da Sprint

#### Daily Scrum
- **Frequência:** Diariamente (exceto weekends)
- **Duração:** 15 minutos máximo
- **Participantes:** Development Team + Scrum Master
- **Objetivo:** Sincronizar atividades e identificar impedimentos

#### Sprint Review
- **Frequência:** Final de cada Sprint (sexta-feira)
- **Duração:** 2 horas máximo
- **Participantes:** Time Scrum + Stakeholders
- **Objetivo:** Demonstrar incremento e coletar feedback

#### Sprint Retrospective
- **Frequência:** Final de cada Sprint (após Review)
- **Duração:** 1.5 horas máximo
- **Participantes:** Time Scrum
- **Objetivo:** Identificar melhorias no processo

### 🎯 Backlog Refinement
- **Frequência:** Meio da Sprint (quarta-feira)
- **Duração:** 1 hora máximo
- **Participantes:** PO + Development Team
- **Objetivo:** Refinar User Stories para próximas Sprints

---

## 🚀 Estratégia de Deploy e Releases

### 🏗️ Ambientes
1. **Desenvolvimento:** Local de cada desenvolvedor
2. **Integração:** Servidor compartilhado para testes
3. **Homologação:** Ambiente espelho da produção
4. **Produção:** Ambiente final para usuários

### 📦 Strategy de Release
- **Sprint 2:** Deploy em homologação (MVP)
- **Sprint 4:** Deploy em produção (versão completa)
- **Sprint 6:** Release final com todas otimizações

### 🔄 CI/CD Pipeline
1. **Push para repositório** → Testes automatizados
2. **Merge para main** → Deploy automático em integração
3. **Tag de release** → Deploy em homologação
4. **Aprovação manual** → Deploy em produção

---

## 🎯 Critérios de Sucesso do Projeto

### ✅ Critérios Funcionais
- [ ] Todas as User Stories implementadas conforme critérios
- [ ] Sistema estável em produção por 1 semana
- [ ] Performance atendendo SLAs definidos
- [ ] Testes de usuário com aprovação > 90%

### ✅ Critérios de Qualidade
- [ ] Cobertura de testes > 80%
- [ ] Zero bugs críticos em produção
- [ ] Code review em 100% do código
- [ ] Documentação técnica completa

### ✅ Critérios de Processo
- [ ] Velocity estável nas últimas 3 Sprints
- [ ] Retrospectivas gerando melhorias implementadas
- [ ] Time auto-organizado e produtivo
- [ ] Stakeholders satisfeitos com entregas

Este planejamento garante a entrega de um sistema robusto e completo no prazo de 3 meses, com incrementos de valor a cada Sprint e feedback contínuo dos usuários.