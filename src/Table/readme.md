# Mapper

Mapper is the last ORM layer which communicates with database. In contrary to repository, mapper is storage specific, even more, also database specific. Everything database specific should be implemented only in mapper layer. 

Use a repository to access methods from mapper layer.
